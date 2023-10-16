<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\AuditReviewersCollection;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Audit Reviewers Model
 */
class AuditReviewersTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('audit_reviewers');

		$this->setPrimaryKey('id');

		$this->setDisplayField('full_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Agencies', [
			'foreignKey' => 'agency_id',
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
				'Agencies' => [
					'OutgoingProfile',
				],
				'CreatedByUser',
				'ModifiedByUser',
			],
			'limitedFields' => [
				'id',
				'agency_id',
				'first_name',
				'middle_name',
				'last_name',
				'title',
				'phone',
				'email',
				'professional_degree',
				'notes',
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
			'collectionClass' => AuditReviewersCollection::class,
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
			->minLength('first_name', 2)
			->maxLength('first_name', 50)
			->notEmptyString('first_name');

		$validator
			->maxLength('middle_name', 50)
			->allowEmptyString('middle_name');

		$validator
			->requirePresence('last_name', 'create')
			->minLength('last_name', 2)
			->maxLength('last_name', 50)
			->notEmptyString('last_name');

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
		$rules->add($rules->existsIn(['agency_id'], 'Agencies'));

		return $rules;
	}
}
