<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\ClientEmployeesCollection;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Client Employees Model
 */
class ClientEmployeesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('client_employees');

		$this->setPrimaryKey('id');

		$this->setDisplayField('full_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->hasMany('Cases', [
			'foreignKey' => 'client_employee_id',
		]);

		$this->belongsTo('Facilities', [
			'foreignKey' => 'facility_id',
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

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'Facilities',
				'CreatedByUser',
				'ModifiedByUser'
			],
			'limitedFields' => [
				'id',
				'client_employee_type_id',
				'facility_id',
				'first_name',
				'last_name',
				'title',
				'work_phone',
				'mobile_phone',
				'email',
				'client_id',
				'npi_number',
				'state',
				'active',
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
				$this->aliasField('last_name') => 'asc',
				$this->aliasField('first_name') => 'asc',
			],
		]);

		// Random Behavior
		$this->addBehavior('Random');

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => ClientEmployeesCollection::class,
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
			->requirePresence('first_name', 'create')
			->minLength('first_name', 1)
			->maxLength('first_name', 50)
			->notEmptyString('first_name');

		$validator
			->requirePresence('last_name', 'create')
			->minLength('last_name', 1)
			->maxLength('last_name', 50)
			->notEmptyString('last_name');

		$validator
			->requirePresence('npi_number', 'create')
			->notEmptyString('npi_number');

		$validator
			->maxLength('title', 50)
			->allowEmptyString('title');

		$validator
			->maxLength('work_phone', 50)
			->allowEmptyString('work_phone');

		$validator
			->maxLength('mobile_phone', 50)
			->allowEmptyString('mobile_phone');

		$validator
			->maxLength('email', 50)
			->allowEmptyString('email');

		$validator
			->maxLength('state', 2)
			->allowEmptyString('state');

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
}
