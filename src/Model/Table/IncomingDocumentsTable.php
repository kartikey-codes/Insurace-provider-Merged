<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\IncomingDocumentListener;
use App\Model\Entity\IncomingDocument;
use App\Model\Filter\IncomingDocumentsCollection;
use Cake\Core\Exception\CakeException;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Incoming Documents Model
 *
 * @property \App\Model\Table\AppealsTable $Appeals
 * @property \App\Model\Table\CasesTable $Cases
 */
class IncomingDocumentsTable extends Table
{
	use LocatorAwareTrait;

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('incoming_documents');

		$this->setPrimaryKey('id');

		$this->setDisplayField('file_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */
		// Belongs To
		$this->belongsTo('Facilities', [
			'foreignKey' => 'facility_id',
		]);

		$this->belongsTo('Cases', [
			'foreignKey' => 'case_id',
		]);

		$this->belongsTo('Appeals', [
			'foreignKey' => 'appeal_id',
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
				 * Belongs To
				 */
				'Facilities' => [
					'FacilityTypes',
				],
				'Appeals' => [
					'AppealLevels',
				],
				'Cases' => [
					'Facilities',
					'Patients',
				],
				'AssignedToUser',
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
				$this->aliasField('created') => 'asc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => IncomingDocumentsCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new IncomingDocumentListener()
		);
	}

	/**
	 * After Save Commit
	 *
	 * @return void
	 */
	public function afterSaveCommit(Event $event, Entity $entity, $options): void
	{
		// New Users
		if ($entity->isNew()) {
			$event = new Event('Model.IncomingDocument.created', $this, ['entity' => $entity]);
			$this->getEventManager()->dispatch($event);
		} else {
			$event = new Event('Model.IncomingDocument.modified', $this, ['entity' => $entity]);
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
			->maxLength('file_name', 255)
			->allowEmptyString('file_name');

		$validator
			->maxLength('original_name', 255)
			->allowEmptyString('original_name');

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
		$rules->add($rules->existsIn(['facility_id'], 'Facilities'));
		$rules->add($rules->existsIn(['case_id'], 'Cases'));
		$rules->add($rules->existsIn(['appeal_id'], 'Appeals'));

		return $rules;
	}

	/**
	 * Find New (Not assigned to Case/Appeal)
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNew(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp
				->isNull('case_id')
				->isNull('appeal_id')
				->isNotNull('file_name');
		});
	}

	/**
	 * Find Unable To Complete
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findUnableToComplete(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('unable_to_complete') => true,
		]);
	}

	/**
	 * Attach an incoming document to an existing Case.
	 *
	 * @param int $id
	 * @param int $caseId
	 * @return \App\Model\Entity\IncomingDocument
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function attachToCase(int $id, int $caseId): IncomingDocument
	{
		if (empty($id)) {
			throw new CakeException(__('An incoming document ID must be provided to attach to a case.'));
		}

		if (empty($caseId)) {
			throw new CakeException(__('A case ID must be provided to attach the incoming document to.'));
		}

		if (!$this->Cases->exists(['id' => $caseId])) {
			throw new CakeException(__('Case does not exist.'));
		}

		$entity = $this->get($id);
		$entity->set('case_id', $caseId);
		$this->saveOrFail($entity);

		return $entity;
	}

	/**
	 * Attach an incoming document to an existing Appeal.
	 *
	 * @param int $id
	 * @param int $appealId
	 * @return \App\Model\Entity\IncomingDocument
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function attachToAppeal(int $id, int $appealId): IncomingDocument
	{
		if (empty($id)) {
			throw new CakeException(__('An incoming document ID must be provided to attach to an appeal.'));
		}

		if (empty($appealId)) {
			throw new CakeException(__('An appeal ID must be provided to attach the incoming document to.'));
		}

		if (!$this->Appeals->exists(['id' => $appealId])) {
			throw new CakeException(__('Appeal does not exist.'));
		}

		$entity = $this->get($id);
		$entity->set('appeal_id', $appealId);
		$this->saveOrFail($entity);

		return $entity;
	}
}
