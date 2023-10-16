<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\AppealListener;
use App\Model\Entity\Appeal;
use App\Model\Filter\AppealsCollection;
use ArrayObject;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use InvalidArgumentException;

/**
 * Appeals Model
 *
 * @property \App\Model\Table\CasesTable $Cases
 */
class AppealsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('appeals');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		// Belongs To
		$this->belongsTo('Cases', [
			'foreignKey' => 'case_id',
			'dependent' => true,
			'cascadeCallbacks' => true,
		]);

		$this->belongsTo('Agencies', [
			'foreignKey' => 'agency_id',
		]);

		$this->belongsTo('Clients', [
			'foreignKey' => 'client_id',
		]);

		$this->belongsTo('AuditReviewers', [
			'foreignKey' => 'audit_reviewer_id',
		]);

		$this->belongsTo('AssignedToVendors', [
			'className' => 'Vendors',
			'foreignKey' => 'assigned_to_vendor_id',
		]);

		$this->belongsTo('AppealTypes', [
			'foreignKey' => 'appeal_type_id',
		]);

		$this->belongsTo('AppealLevels', [
			'foreignKey' => 'appeal_level_id',
		]);

		$this->belongsTo('CompletedByUser', [
			'foreignKey' => 'completed_by',
			'className' => 'Users',
		]);

		$this->belongsTo('DaysToRespondFroms', [
			'foreignKey' => 'days_to_respond_from_id',
		]);

		// Belongs To Many

		$this->belongsToMany('NotDefendableReasons', [
			'through' => 'AppealNotDefendableReasons',
			'foreignKey' => 'appeal_id',
			'targetForeignKey' => 'not_defendable_reason_id',
		]);

		$this->belongsToMany('UtcReasons', [
			'through' => 'AppealUtcReasons',
			'foreignKey' => 'appeal_id',
			'targetForeignKey' => 'utc_reason_id',
		]);

		// Has Many

		$this->hasMany('AppealReferenceNumbers', [
			'foreignKey' => 'appeal_id',
			'saveStrategy' => 'replace',
		]);

		$this->hasMany('AppealNotes', [
			'foreignKey' => 'appeal_id',
			'strategy' => 'select',
		]);

		$this->hasMany('IncomingDocuments', [
			'foreignKey' => 'appeal_id',
		]);

		$this->hasMany('GuestPortals', [
			'foreignKey' => 'appeal_id',
			'strategy' => 'select',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Date
		$this->addBehavior('Date');

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'limitedFields' => [
				'id',
				'case_id',
				'appeal_type_id',
				'appeal_level_id',
				'defendable',
				'letter_date',
				'received_date',
				'due_date',
				'hearing_date',
				'hearing_time',
				'days_to_respond',
				'priority',
				'assigned_to',
				'completed',
				'appeal_status',
				'cancelled',
				'submitted',
				'closed',
				'unable_to_complete',
				'audit_reviewer_id',
				'agency_id',
				'audit_identifier',
			],
			'fullContain' => [
				'Agencies' => [
					'finder' => 'limited'
				],
				'Cases' => [
					'CaseTypes' => [
						'finder' => 'limited'
					],
					'Patients' => [
						'finder' => 'limited'
					],
					'Facilities' => [
						'finder' => 'limited'
					],
					'DenialTypes' => [
						'finder' => 'limited'
					],
					'CaseOutcomes' => [
						'finder' => 'limited'
					],
					'InsuranceProviders' => [
						'finder' => 'limited'
					],
					'InsuranceTypes' => [
						'finder' => 'limited',
						'InsuranceProvider',
					],
				],
				'AppealTypes' => [
					'finder' => 'limited'
				],
				'AppealLevels' => [
					'finder' => 'limited'
				],
				'AuditReviewers' => [
					'finder' => 'limited',
					'Agencies' => [
						'OutgoingProfile'
					]
				],
				'AssignedToUser' => [
					'finder' => 'limited'
				],
				'GuestPortals',
				'IncomingDocuments',
				'NotDefendableReasons' => [
					'finder' => 'ordered',
				],
				'UtcReasons' => [
					'finder' => 'ordered',
				],
				'AppealReferenceNumbers' => [
					'ReferenceNumbers',
				],
				'AppealNotes' => [
					'finder' => 'orderedAndFull',
				],
				'CompletedByUser' => [
					'finder' => 'ordered',
				],
				'CreatedByUser' => [
					'finder' => 'ordered',
				],
				'DaysToRespondFroms' => [
					'finder' => 'ordered',
				],
				'ModifiedByUser' => [
					'finder' => 'ordered',
				],
			],
		]);

		// Assignable
		$this->addBehavior('Assignable');

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// User Auditing
		$this->addBehavior('UserAudit');

		// Timestamp Behavior
		$this->addBehavior('Timestamp', [
			'events' => [
				'Model.beforeSave' => [
					'created' => 'new',
					'modified' => 'always',
				],
			],
		]);

		// Ordered
		$this->addBehavior('Orderable', [
			'default' => [
				'AppealLevels.order_number' => 'asc',
				$this->aliasField('letter_date') => 'desc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => AppealsCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new AppealListener()
		);
	}

	/**
	 * Logic to execute before saving
	 *
	 * @param \Cake\Event\EventInterface $event The beforeSave event.
	 * @param \Cake\ORM\Entity $entity The entity of this table that we're working with.
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($entity->defendable === true || $entity->defendable === null) {
			$entity->set('defendable_reasons', ['_ids' => []]);
		}

		if ($entity->unable_to_complete === false || $entity->unable_to_complete === null) {
			$entity->set('utc_reasons', ['_ids' => []]);
		}
	}

	/**
	 * After Save
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($entity->isDirty('completed') && !empty($entity->completed)) {
			$event = new Event('Model.Appeal.completed', $this, compact('entity'));
			$this->getEventManager()->dispatch($event);
		}
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->add('id', 'valid', ['rule' => 'numeric'])
			->allowEmptyString('id', 'create');

		$validator
			->add('case_id', 'valid', ['rule' => 'numeric'])
			->notEmptyString('case_id', 'create');

		$validator
			->add('client_id', 'valid', ['rule' => 'numeric'])
			->notEmptyString('client_id', 'create');

		$validator
			->add('audit_reviewer_id', 'valid', ['rule' => 'numeric'])
			->allowEmptyString('audit_reviewer_id', 'create');

		return $validator;
	}

	/**
	 * Returns a rules checker object that will be used for validating
	 * application integrity.
	 *
	 * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
	 * @return \Cake\ORM\RulesChecker
	 */
	public function buildRules(RulesChecker $rules): RulesChecker
	{
		$rules->add($rules->existsIn(['client_id'], 'Clients'));

		$rules->add($rules->existsIn(['case_id'], 'Cases'));
		$rules->add($rules->existsIn(['appeal_type_id'], 'AppealTypes'));
		$rules->add($rules->existsIn(['appeal_level_id'], 'AppealLevels'));
		$rules->add($rules->existsIn(['days_to_respond_from_id'], 'DaysToRespondFroms'));
		$rules->add($rules->existsIn(['audit_reviewer_id'], 'AuditReviewers'));

		$rules->add(function (Appeal $appeal) {
			$keys = $appeal->getStatuses();

			return in_array($appeal->appeal_status, $keys);
		}, [
			'errorField' => 'appeal_status',
			'message' => __('Invalid appeal status'),
		]);

		return $rules;
	}

	/**
	 * Finder for queue. Should be incomplete and ordered by priority
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findQueue(Query $query, array $options): Query
	{
		return $query->find('notCompleted', $options)
			->find('notVoided', $options)
			->find('queueOrder');
	}

	/**
	 * Find Open Grouped By Facility
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findOpenByFacility(Query $query, array $options): Query
	{
		return $query
			->find('open', $options)
			->select([
				'count' => $query->func()->count(
					$this->aliasField('id')
				),
				// Cake ORM doesn't seem to be placing these fields into an association array
				// such as [Facilities].[id], [Facilities.name] so we're manually bringing them in
				'facility_id' => $this->Cases->Facilities->aliasField('id'),
				'facility_name' => $this->Cases->Facilities->aliasField('name'),
			])
			->group([
				$this->Cases->Facilities->aliasField('id'),
				$this->Cases->Facilities->aliasField('name'),
			])
			->innerJoinWith('Cases', function ($q) {
				return $q->find('open')->innerJoinWith('Facilities');
			})
			->enableHydration(false);
	}

	/**
	 * Find By Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByStatus(Query $query, array $options): Query
	{
		if (empty($options['status'])) {
			throw new BadRequestException(__('No status provided'));
		}

		return $query->where([
			$this->aliasField('appeal_status') => $options['status'],
		]);
	}

	/**
	 * Find Open Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findOpen(Query $query, array $options): Query
	{
		return $query->find('notCompleted', $options);
	}

	/**
	 * Find Completed Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findCompleted(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNotNull($this->aliasField('completed'));
		});
	}

	/**
	 * Find UTC Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findUtc(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('unable_to_complete'), true);
		});
	}

	/**
	 * Find Cancelled Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findCancelled(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNotNull($this->aliasField('cancelled'));
		});
	}

	/**
	 * Find Closed Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findClosed(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNotNull($this->aliasField('closed'));
		});
	}

	/**
	 * Find Not Completed Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNotCompleted(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp
				->isNull($this->aliasField('cancelled'))
				->isNull($this->aliasField('completed'))
				->isNull($this->aliasField('closed'));
		});
	}

	/**
	 * Find Not Voided Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNotVoided(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNull($this->aliasField('cancelled'));
		});
	}

	/**
	 * Find Not in a finished status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNotFinished(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->notIn($this->aliasField('appeal_status'), [
				Appeal::STATUS_CANCELLED,
				Appeal::STATUS_CLOSED,
			]);
		});
	}

	/**
	 * Find Defendable
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findDefendable(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('defendable'), true);
		});
	}

	/**
	 * Find Not Defendable
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNotDefendable(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('defendable'), false);
		});
	}

	/**
	 * Finder for oldest to newest, primarily for queue
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findQueueOrder(Query $query, array $options): Query
	{
		return $query->order([
			$this->aliasField('priority') => 'desc',
			$this->aliasField('due_date') => 'asc',
		], true);
	}

	/**
	 * Finder for basic associations. Generally for logging or searching results.
	 * Should ignore hasMany associations.
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findBasic(Query $query, array $options): Query
	{
		return $query->contain([
			'Cases' => [
				'Facilities',
				'Patients',
			],
			'AppealLevels',
		]);
	}

	/**
	 * Finder for appeals submitted to vendor service
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findSubmitted(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('appeal_status') => Appeal::STATUS_SUBMITTED,
		]);
	}

	/**
	 * Finder for appeals assigned
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssigned(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('appeal_status') => Appeal::STATUS_ASSIGNED,
		]);
	}

	/**
	 * Finder for appeals assigned to a vendor
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssignedToVendor(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNotNull($this->aliasField('assigned_to_vendor_id'));
		});
	}

	/**
	 * Finder for appeals assigned to a specific vendor
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssignedToVendorById(Query $query, array $options): Query
	{
		if (empty($options['id'])) {
			throw new InvalidArgumentException(__('A vendor ID was not provided as the `id` parameter'));
		}

		return $query->where([
			$this->aliasField('assigned_to_vendor_id') => $options['id'],
		]);
	}

	/**
	 * Finder for appeals submitted to vendor service
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNotAssignedToVendor(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNull($this->aliasField('assigned_to_vendor_id'));
		});
	}

	/**
	 * Finder for appeals assignable to a vendor (all statuses correct)
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssignableToVendor(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('appeal_status') => Appeal::STATUS_SUBMITTED,
			$this->aliasField('assigned_to_vendor_id') . ' IS' => null,
		]);
	}

	/**
	 * Finder for appeals resubmitted to vendors
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findResubmitted(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('appeal_status') => Appeal::STATUS_SUBMITTED,
			$this->aliasField('assigned_to_vendor_id') . ' IS NOT' => null,
		]);
	}
}
