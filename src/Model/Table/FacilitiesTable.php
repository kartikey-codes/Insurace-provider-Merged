<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\FacilitiesCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Facilities Model
 */
class FacilitiesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('facilities');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		// Belongs To
		$this->belongsTo('FacilityTypes', [
			'foreignKey' => 'facility_type_id',
		]);

		// Has Many

		$this->hasMany('Cases', [
			'foreignKey' => 'facility_id',
		]);

		$this->hasMany('IncomingDocuments', [
			'foreignKey' => 'facility_id',
		]);

		$this->hasMany('ClientEmployees', [
			'foreignKey' => 'facility_id',
		]);

		// Belongs To Many

		$this->belongsToMany('Services', [
			'through' => 'FacilitiesServices',
			'foreignKey' => 'facility_id',
			'targetForeignKey' => 'service_id',
			'saveStrategy' => 'replace',
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
			'key' => 'Facilities',
			'stores' => ['all', 'active'],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'FacilityTypes',
				'ClientEmployees',
				'Services',
				'CreatedByUser',
				'ModifiedByUser',
			],
			'limitedFields' => [
				'id',
				'client_id',
				'facility_type_id',
				'name',
				'phone',
				'fax',
				'email',
				'street_address_1',
				'street_address_2',
				'city',
				'state',
				'zip',
				'active',
				'npi_number',
				'npi_manual',
				'primary_taxonomy',
				'client_owned',
				'chain_name',
				'area_name',
				'ou_number',
				'territory',
				'rvp_name',
				'has_contract',
				'contract_start_date',
				'contract_end_date',
				'indemnification_days',
				'max_return_work_days',
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
			'collectionClass' => FacilitiesCollection::class,
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
			->allowEmptyString('id');

		$validator
			->scalar('name')
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
			->allowEmptyString('phone')
			->maxLength('phone', 50);

		$validator
			->allowEmptyString('fax')
			->maxLength('fax', 50);

		$validator
			->allowEmptyString('email')
			->maxLength('email', 50);

		$validator
			->allowEmptyString('street_address_1')
			->maxLength('street_address_1', 50);

		$validator
			->allowEmptyString('street_address_2')
			->maxLength('street_address_2', 50);

		$validator
			->allowEmptyString('city')
			->maxLength('city', 50);

		$validator
			->allowEmptyString('state')
			->maxLength('state', 2);

		$validator
			->allowEmptyString('zip')
			->maxLength('zip', 20);

		$validator
			->scalar('active')
			->boolean('active');

		$validator
			->allowEmptyString('chain_name')
			->maxLength('chain_name', 255);

		$validator
			->allowEmptyString('area_name')
			->maxLength('area_name', 60);

		$validator
			->allowEmptyString('ou_number')
			->maxLength('ou_number', 60);

		$validator
			->allowEmptyString('territory')
			->maxLength('territory', 60);

		$validator
			->allowEmptyString('rvp_name')
			->maxLength('rvp_name', 60);

		$validator
			->scalar('has_contract')
			->boolean('has_contract');

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
		$rules->add($rules->existsIn(['facility_type_id'], 'FacilityTypes'));

		return $rules;
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
		return $query->contain([]);
	}
}
