<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\DenialReasonsCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Denial Reasons Model
 *
 * @property \App\Model\Table\CasesTable $Cases
 */
class DenialReasonsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('denial_reasons');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsToMany('Cases', [
			'through' => 'CaseDenialReasons',
			'foreignKey' => 'denial_reason_id',
			'targetForeignKey' => 'case_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'CaseTypes',
			'stores' => ['all'],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [],
			'limitedFields' => [
				'id',
				'name',
			],
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
				$this->aliasField('name') => 'asc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => DenialReasonsCollection::class,
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
			->add('name', [
				'unique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => __('This name is already taken.'),
				],
			])
			->maxLength('name', 250)
			->requirePresence('name', 'create')
			->notEmptyString('name');

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
		$rules->add($rules->isUnique(['name']));

		return $rules;
	}

	/**
	 * Finder for general ordering
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByCase(Query $query, array $options): Query
	{
		return $query->matching('Cases', function ($q) use ($options) {
			return $q->where([
				$this->Cases->aliasField('id') => $options['case_id'],
			]);
		});
	}
}
