<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\InsuranceTypesCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Insurance Types Model
 *
 * @property \App\Model\Table\InsuranceProvidersTable $InsuranceProviders
 */
class InsuranceTypesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('insurance_types');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */
		// Belongs To
		// Disabled as it was moved to insurance providers belongs to many
		$this->belongsTo('InsuranceProvider', [
			'className' => 'InsuranceProviders',
			'foreignKey' => 'insurance_provider_id',
		]);

		// Belongs To Many

		$this->belongsToMany('InsuranceProviders', [
			'through' => 'InsuranceProviderInsuranceTypes',
			'foreignKey' => 'insurance_type_id',
			'targetForeignKey' => 'insurance_provider_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'InsuranceTypes',
			'stores' => ['all'],
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'InsuranceProviders',
			],
			'limitedFields' => [
				'id',
				'name',
				'use_provider_guidelines',
				'insurance_provider_id',
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
				$this->aliasField('name') => 'asc',
			],
		]);

		// Random
		$this->addBehavior('Random');

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
			'collectionClass' => InsuranceTypesCollection::class,
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
			->add('insurance_provider_id', 'valid', ['rule' => 'numeric'])
			->allowEmptyString('insurance_provider_id');

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

		$rules->add($rules->existsIn(['insurance_provider_id'], 'InsuranceProviders'));

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
		return $query->matching('InsuranceProviders', function ($q) use ($options) {
			return $q->where([
				$this->InsuranceProviders->aliasField('id') => $options['insurance_provider_id'],
			]);
		});
	}
}
