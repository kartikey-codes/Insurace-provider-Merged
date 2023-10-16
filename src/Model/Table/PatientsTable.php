<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\PatientsCollection;
use ArrayObject;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use InvalidArgumentException;

/**
 * Patients Model
 */
class PatientsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('patients');

		$this->setPrimaryKey('id');

		$this->setDisplayField('full_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->hasMany('Cases', [
			'foreignKey' => 'patient_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Belongs To Client
		$this->addBehavior('BelongsToClient');

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'CreatedByUser',
				'ModifiedByUser',
			],
			'limitedFields' => [
				'id',
				'first_name',
				'middle_name',
				'last_name',
				'date_of_birth',
				'sex',
				'marital_status',
				'secured',
				'medical_record_number',
				'ssn_last_four',
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
			'collectionClass' => PatientsCollection::class,
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
			->minLength('first_name', 1)
			->maxLength('first_name', 50)
			->requirePresence('first_name', 'create')
			->notEmptyString('first_name');

		$validator
			->maxLength('middle_name', 50)
			->allowEmptyString('middle_name');

		$validator
			->minLength('last_name', 1)
			->maxLength('last_name', 50)
			->requirePresence('last_name', 'create')
			->notEmptyString('last_name');

		$validator
			->allowEmptyString('date_of_birth')
			->date('date_of_birth')
			->add('date_of_birth', 'dob_in_past', [
				'rule' => function ($value, $context) {
					$date = new FrozenDate($value);

					return $date->isPast();
				},
				'message' => __('Date of birth must be in the past.'),
			]);

		$validator
			->maxLength('sex', 16)
			->allowEmptyString('sex');

		$validator
			->maxLength('marital_status', 50)
			->allowEmptyString('marital_status');

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
			->maxLength('medical_record_number', 50)
			->allowEmptyString('medical_record_number');

		$validator
			->maxLength('ssn_last_four', 4)
			->allowEmptyString('ssn_last_four');

		return $validator;
	}

	/**
	 * Logic to execute before saving
	 *
	 * @param \Cake\Event\Event $event The beforeSave event.
	 * @param \Cake\ORM\Entity $entity The entity of this table that we're working with.
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($entity->isNew() && $entity->secured === null) {
			$entity->set('secured', false);
		}
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
	 * Find patients with matching names / birthdates /etc
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findSimilar(Query $query, array $options): Query
	{
		// Date field is required
		if (empty($options['PatientId'])) {
			throw new InvalidArgumentException(__('A patient ID must be provided to find similar.'));
		}

		$entity = $this->find()
			->select([
				$this->aliasField('first_name'),
				$this->aliasField('last_name'),
			])
			->where([
				$this->aliasField('id') => $options['PatientId'],
			])
			->firstOrFail();

		return $query->where(function (QueryExpression $exp, Query $q) use ($options, $entity) {
			return $exp
				->notEq($this->aliasField('id'), $options['PatientId'])
				->like($this->aliasField('first_name'), $entity->first_name)
				->like($this->aliasField('last_name'), $entity->last_name);
		});
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
