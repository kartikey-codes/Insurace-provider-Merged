<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\OutgoingDocumentListener;
use App\Model\Entity\OutgoingDocument;
use App\Model\Filter\OutgoingDocumentsCollection;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Outgoing Documents Model
 *
 * @property \App\Model\Table\AppealsTable $Appeals
 * @property \App\Model\Table\CasesTable $Cases
 */
class OutgoingDocumentsTable extends Table
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
		$this->setTable('outgoing_documents');

		$this->setPrimaryKey('id');

		$this->setDisplayField('file_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		// Belongs To
		$this->belongsTo('Cases', [
			'foreignKey' => 'case_id',
		]);

		$this->belongsTo('Appeals', [
			'foreignKey' => 'appeal_id',
		]);

		$this->belongsTo('Agencies', [
			'foreignKey' => 'agency_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				/**
				 * Belongs To
				 */
				'Cases' => [
					'Patients',
				],
				'Appeals',
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
			'collectionClass' => OutgoingDocumentsCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new OutgoingDocumentListener()
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
			$event = new Event('Model.OutgoingDocument.created', $this, ['entity' => $entity]);
			$this->getEventManager()->dispatch($event);
		} else {
			$event = new Event('Model.OutgoingDocument.modified', $this, ['entity' => $entity]);
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
		$rules->add($rules->existsIn(['case_id'], 'Cases'));
		$rules->add($rules->existsIn(['appeal_id'], 'Appeals'));

		return $rules;
	}

	/**
	 * Find New (Not delivered/sent)
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNew(Query $query, array $options): Query
	{
		// return $query->where(function (QueryExpression $exp, Query $q) {
		// 	return $exp
		// 		->isNull($this->aliasField('processed'))
		// 		->isNull($this->aliasField('completed'))
		// 		->isNull($this->aliasField('cancelled'));
		// });

		return $query->where([
			$this->aliasField('status_message') => OutgoingDocument::STATUS_NEW
		]);
	}

	/**
	 * Find Queued
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findQueued(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('status_message') => OutgoingDocument::STATUS_QUEUED,
		]);
	}

	/**
	 * Find Failed
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findFailed(Query $query, array $options): Query
	{
		return $query->where([
			$this->aliasField('status_message') => OutgoingDocument::STATUS_FAILED,
		]);
	}
}
