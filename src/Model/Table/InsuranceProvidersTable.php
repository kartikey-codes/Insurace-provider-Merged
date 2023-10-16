<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\InsuranceProvidersCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Insurance Providers Model
 *
 * @property \App\Model\Table\AppealLevelsTable $AppealLevels
 * @property \App\Model\Table\InsuranceTypesTable $InsuranceTypes
 */
class InsuranceProvidersTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('insurance_providers');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */
		// Belongs To
		$this->belongsTo('DefaultInsuranceType', [
			'className' => 'InsuranceTypes',
			'foreignKey' => 'default_insurance_type_id',
		]);

		// Belongs To Many

		$this->belongsToMany('Agencies', [
			'className' => 'Agencies',
			'through' => 'InsuranceProviderAppealLevels',
			'foreignKey' => 'insurance_provider_id',
			'targetForeignKey' => 'agency_id'
		]);

		$this->belongsToMany('AppealLevels', [
			'through' => 'InsuranceProviderAppealLevels',
			'foreignKey' => 'insurance_provider_id',
			'targetForeignKey' => 'appeal_level_id',
			'saveStrategy' => 'replace',
		]);

		$this->belongsToMany('InsuranceTypes', [
			'through' => 'InsuranceProviderInsuranceTypes',
			'foreignKey' => 'insurance_provider_id',
			'targetForeignKey' => 'insurance_type_id',
			'saveStrategy' => 'replace',
		]);

		// Has Many

		$this->hasMany('Cases', [
			'foreignKey' => 'insurance_provider_id',
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

		// Belongs To Client
		$this->addBehavior('BelongsToClient');

		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'InsuranceProviders',
			'stores' => ['all', 'active'],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'AppealLevels' => [
					'finder' => 'ordered',
					'Agencies',
				],
				'InsuranceTypes' => [
					'finder' => 'ordered',
				],
				'DefaultInsuranceType',
				'CreatedByUser',
				'ModifiedByUser',
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

		// Random Behavior
		$this->addBehavior('Random');

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => InsuranceProvidersCollection::class,
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
			->minLength('name', 2)
			->maxLength('name', 50)
			->requirePresence('name', 'create')
			->notEmptyString('name');

		$validator
			->maxLength('phone', 50)
			->allowEmptyString('phone');

		$validator
			->maxLength('fax', 50)
			->allowEmptyString('fax');

		$validator
			->maxLength('street_address_1', 50)
			->allowEmptyString('street_address_1');

		$validator
			->maxLength('street_address_2', 50)
			->allowEmptyString('street_address_2');

		$validator
			->maxLength('city', 50)
			->allowEmptyString('city');

		$validator
			->maxLength('state', 2)
			->allowEmptyString('state');

		$validator
			->maxLength('zip', 20)
			->allowEmptyString('zip');

		$validator
			->maxLength('email', 50)
			->allowEmptyString('email');

		$validator
			->maxLength('website', 80)
			->allowEmptyString('website');

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

		$rules->add($rules->existsIn(['default_insurance_type_id'], 'InsuranceTypes'));

		return $rules;
	}

	/**
	 * Finder for matching an appeal level
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAppealLevel(Query $query, array $options): Query
	{
		return $query->matching('AppealLevels', function ($q) use ($options) {
			return $q->where([
				$this->AppealLevels->aliasField('id') => $options['appeal_level_id'],
			]);
		});
	}

	/**
	 * Finder for matching an insurance type
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findInsuranceType(Query $query, array $options): Query
	{
		return $query->matching('InsuranceTypes', function ($q) use ($options) {
			return $q->where([
				$this->InsuranceTypes->aliasField('id') => $options['insurance_type_id'],
			]);
		});
	}
}
