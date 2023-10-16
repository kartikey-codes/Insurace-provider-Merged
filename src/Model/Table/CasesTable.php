<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\CaseEntity;
use App\Model\Entity\User;
use App\Model\Filter\CasesCollection;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cases Model
 *
 * @property \App\Model\Table\AppealsTable $Appeals
 */
class CasesTable extends Table
{
	/**
	 * Can't use 'Case' as a PHP class name, so we override this to CaseEntity
	 *
	 * @var string
	 */
	protected $entityClass = 'CaseEntity';

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('cases');

		$this->setEntityClass(CaseEntity::class);

		$this->setPrimaryKey('id');

		$this->setDisplayField('admit_date');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		// Belongs To
		$this->belongsTo('ClientEmployees', [
			'foreignKey' => 'client_employee_id',
		]);

		$this->belongsTo('CaseTypes', [
			'foreignKey' => 'case_type_id',
		]);

		$this->belongsTo('Clients', [
			'foreignKey' => 'client_id',
		]);

		$this->belongsTo('Patients', [
			'foreignKey' => 'patient_id',
		]);

		$this->belongsTo('Facilities', [
			'foreignKey' => 'facility_id',
		]);

		$this->belongsTo('DenialTypes', [
			'foreignKey' => 'denial_type_id',
		]);

		$this->belongsTo('CaseOutcomes', [
			'foreignKey' => 'case_outcome_id',
		]);

		$this->belongsTo('InsuranceProviders', [
			'foreignKey' => 'insurance_provider_id',
		]);

		$this->belongsTo('InsuranceTypes', [
			'foreignKey' => 'insurance_type_id',
		]);

		$this->belongsTo('ClosedByUser', [
			'foreignKey' => 'closed_by',
			'className' => 'Users',
		]);

		// Belongs To Many

		$this->belongsToMany('ActiveUsers', [
			'className' => 'Users',
			'through' => 'CaseActivity',
			'foreignKey' => 'case_id',
			'targetForeignKey' => 'user_id',
			'finder' => 'current',
		]);

		$this->belongsToMany('DenialReasons', [
			'through' => 'CaseDenialReasons',
			'foreignKey' => 'case_id',
			'targetForeignKey' => 'denial_reason_id',
			'saveStrategy' => 'replace',
		]);

		$this->belongsToMany('Disciplines', [
			'through' => 'CasesDisciplines',
			'foreignKey' => 'case_id',
			'targetForeignKey' => 'discipline_id',
			'saveStrategy' => 'replace',
		]);

		// Has Many

		$this->hasMany('Appeals', [
			'foreignKey' => 'case_id',
			'dependent' => true,
			'cascadeCallbacks' => true
		]);

		$this->hasMany('CaseReadmissions', [
			'foreignKey' => 'case_id',
			'saveStrategy' => 'replace',
		]);

		$this->hasMany('CaseRequests', [
			'foreignKey' => 'case_id',
			'dependent' => true,
			'cascadeCallbacks' => true
		]);

		$this->hasMany('IncomingDocuments', [
			'foreignKey' => 'case_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Assignable
		$this->addBehavior('Assignable');

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				/**
				 * Associations
				 */

				// Belongs To
				'CaseTypes',
				'ClientEmployees',
				'Patients',
				'Facilities' => [
					'FacilityTypes',
				],
				'DenialTypes',
				'DenialReasons',
				'Disciplines',
				'CaseOutcomes',
				'InsuranceProviders' => [
					'InsuranceTypes',
					'AppealLevels',
				],
				'InsuranceTypes' => [
					'InsuranceProvider',
					'InsuranceProviders',
				],
				'IncomingDocuments' => [
					'finder' => 'ordered',
					'Facilities',
				],
				'CaseReadmissions',
				'ClosedByUser' => [
					'finder' => 'limited'
				],
				'CreatedByUser' => [
					'finder' => 'limited'
				],
				'ModifiedByUser' => [
					'finder' => 'limited'
				],
				'AssignedToUser' => [
					'finder' => 'limited'
				],

				// Belongs To Many
				// None

				// Has Many
				'Appeals' => [
					'finder' => 'ordered',
					'AppealTypes',
					'AppealLevels',
					'AssignedToUser',
					'DaysToRespondFroms',
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
					'CompletedByUser',
					'CreatedByUser',
					'ModifiedByUser',
				],
				'CaseRequests' => [],
			],
		]);

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

		// Orderable
		$this->addBehavior('Orderable', [
			'default' => [
				$this->aliasField('admit_date') => 'desc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => CasesCollection::class,
		]);
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
			->allowEmptyString('admit_date')
			->date('admit_date')
			->add('admit_date', 'in_past', [
				'rule' => function ($value, $context) {
					$date = new FrozenDate($value);

					return $date->isPast();
				},
				'message' => __('Must be in the past'),
				'on' => 'create',
			]);

		$validator
			->allowEmptyString('discharge_date')
			->date('discharge_date');

		$validator
			->add('client_id', 'valid', ['rule' => 'numeric'])
			->notEmptyString('client_id', 'create');

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

		return $rules;
	}

	/**
	 * Finder for matching a denial reason
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByDenialReason(Query $query, array $options): Query
	{
		return $query->matching('DenialReasons', function ($q) use ($options) {
			return $q->where([
				$this->DenialReasons->aliasField('id') => $options['denial_reason_id'],
			]);
		});
	}

	/**
	 * Find Open Status
	 *
	 * @todo Change to anonymous query function
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findOpen(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('closed') . ' IS' => null,
			'OR' => [
				$this->aliasField('unable_to_complete') . ' IS' => null,
				$this->aliasField('unable_to_complete') => false
			]
		]);
	}

	/**
	 * Find UTC Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findUTC(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('unable_to_complete'), true);
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
			return $exp->isNotNull('closed');
		});
	}

	/**
	 * Find Having Appeals
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findHavingAppeals(Query $query, array $options): Query
	{
		return $query->matching('Appeals');
	}

	/**
	 * Find Without Appeals
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findWithoutAppeals(Query $query, array $options): Query
	{
		return $query->notMatching('Appeals');
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
			'Facilities',
			'Patients',
		]);
	}

	/**
	 * Close out all appeals under a case
	 *
	 * @param string $id
	 * @param \App\Model\Entity\User $user
	 * @return bool
	 */
	public function closeAllAppeals($id, User $user): bool
	{
		$case = $this->get($id, [
			'contain' => [
				'Appeals',
			],
		]);

		/** @var \App\Model\Entity\Appeal */
		foreach ($case->appeals as $appeal) {
			$entity = $appeal->setClosedBy($user);
			$this->Appeals->saveOrFail($entity);
		}

		return true;
	}
}
