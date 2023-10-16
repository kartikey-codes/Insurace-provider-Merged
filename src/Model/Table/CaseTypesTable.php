<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\CaseTypesCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Case Types Model
 *
 * @property \App\Model\Table\CasesTable $Cases
 */
class CaseTypesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('case_types');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->hasMany('Cases', [
			'foreignKey' => 'case_type_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Active
		$this->addBehavior('Active', [
			'field' => $this->aliasField('active'),
		]);

		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'CaseTypes',
			'stores' => ['all'],
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'limitedFields' => [
				'id',
				'name',
				'active',
				'short_name',
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
				$this->aliasField('name') => 'asc',
			],
		]);

		// Random Behavior
		$this->addBehavior('Random');

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => CaseTypesCollection::class,
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
			->requirePresence('name', 'create')
			->notEmptyString('name');

		$validator
			->add('short_name', [
				'unique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => __('This short name is already taken.'),
				],
			])
			->requirePresence('short_name', 'create')
			->notEmptyString('short_name');

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
		$rules->add($rules->isUnique(['short_name']));

		return $rules;
	}

	/**
	 * Finder for counting appeals under this case
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findCountMatchingAppeals(Query $query, array $options): Query
	{
		$key = 'appeals';
		if (!empty($options['key'])) {
			$key = $options['key'];
		}

		return $query
			->leftJoinWith('Cases.Appeals', $options['query'])
			->select([
				'id' => $this->aliasField('id'),
				'name' => $this->aliasField('name'),
				$key => $query->func()->count(
					$query->identifier($this->Cases->Appeals->aliasField('id'))
				),
			])
			->group([
				$this->aliasField('id'),
				$this->aliasField('name'),
			])
			->order([
				$key => 'DESC'
			]);
	}
}
