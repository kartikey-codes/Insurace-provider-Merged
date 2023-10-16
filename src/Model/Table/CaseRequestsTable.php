<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\CaseRequestsCollection;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Case Requests Model
 */
class CaseRequestsTable extends Table
{
	/**
	 * @var array
	 */
	public array $requestTypes = [
		'DOCUMENTATION',
		'HEARING'
	];

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('case_requests');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Fields
		 * -------------------------------------
		 */
		$this->getSchema()->setColumnType('due_date', 'date');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Agencies', [
			'foreignKey' => 'agency_id',
		]);

		$this->belongsTo('Cases', [
			'foreignKey' => 'case_id',
		]);

		$this->belongsTo('CompletedByUser', [
			'foreignKey' => 'completed_by',
			'className' => 'Users',
		]);

		$this->belongsTo('InsuranceProviders', [
			'foreignKey' => 'insurance_provider_id',
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
				'CreatedByUser',
				'ModifiedByUser',
				'CompletedByUser',
				'AssignedToUser',
				'Agencies',
				'InsuranceProviders'
			],
			'limitedFields' => [
				'id',
				'case_id',
				'client_id',
				'request_type',
				'name',
				'unable_to_complete',
				'due_date',
				'completed',
				'completed_at',
				'completed_by',
				'agency_id',
				'insurance_provider_id',
				'created',
				'modified',
				'deleted',
				'assigned',
				'assigned_to'
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
				$this->aliasField('due_date') => 'asc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => CaseRequestsCollection::class,
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
			->requirePresence('name', 'create')
			->minLength('name', 2, __('Must be at least 2 characters'))
			->notEmptyString('name', __('A name label is required'));

		$validator
			->inList('request_type', array_values($this->requestTypes), __('Must be a valid request type'), 'create')
			->notEmptyString('request_type');

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
			->find('queueOrder', $options);
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
		return $query->where(function (QueryExpression $exp, Query $q) {
			return
				$exp->eq($this->aliasField('completed'), false)
				->eq($this->aliasField('unable_to_complete'), false);
		});
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
	 * Find Completed Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findCompleted(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('completed'), true)
				->eq($this->aliasField('unable_to_complete'), false);
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
			return $exp->eq($this->aliasField('completed'), false);
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
			$this->aliasField('due_date') => 'asc',
		]);
	}
}
