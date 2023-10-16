<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\AppealLevelsCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Appeal Levels Model
 *
 * @property \App\Model\Table\AppealsTable $Appeals
 * @property \App\Model\Table\InsuranceProvidersTable $InsuranceProviders
 */
class AppealLevelsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('appeal_levels');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->hasMany('Appeals', [
			'foreignKey' => 'appeal_level_id',
		]);

		$this->belongsToMany('InsuranceProviders', [
			'through' => 'InsuranceProviderAppealLevels',
			'foreignKey' => 'appeal_level_id',
			'targetForeignKey' => 'insurance_provider_id',
		]);

		$this->belongsToMany('Agencies', [
			'className' => 'Agencies',
			'through' => 'InsuranceProviderAppealLevels',
			'foreignKey' => 'appeal_level_id',
			'targetForeignKey' => 'agency_id'
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
			'key' => 'AppealLevels',
			'stores' => ['active', 'all'],
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'limitedFields' => [
				'id',
				'name',
				'short_name',
				'description',
				'active'
			],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// User Auditing
		$this->addBehavior('UserAudit');

		// Orderable
		$this->addBehavior('Orderable', [
			'default' => [
				$this->aliasField('order_number') => 'ASC',
				$this->aliasField('name') => 'ASC',
			],
		]);

		// Timestamp Behavior
		$this->addBehavior('Timestamp', [
			'events' => [
				'Model.beforeSave' => [
					'created' => 'new',
					'modified' => 'always',
				],
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => AppealLevelsCollection::class,
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
	 * Finder for general ordering
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByInsuranceProvider(Query $query, array $options): Query
	{
		return $query->matching('InsuranceProviders', function (Query $q) use ($options) {
			return $q->where([
				$this->InsuranceProviders->aliasField('id') => $options['insurance_provider_id'],
			]);
		});
	}
}
