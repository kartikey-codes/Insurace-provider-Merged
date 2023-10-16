<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\AgencyListener;
use App\Model\Entity\Agency;
use App\Lib\LocationUtility\LocationUtility;
use App\Model\Filter\AgenciesCollection;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Agencies Model
 */
class AgenciesTable extends Table
{
	use LocatorAwareTrait;

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('agencies');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->hasOne('OutgoingProfile', [
			'className' => 'AgencyOutgoingProfile',
			'foreignKey' => 'agency_id',
			'dependent' => true,
		]);

		$this->hasMany('AuditReviewers', [
			'foreignKey' => 'agency_id',
		]);

		$this->hasMany('Appeals', [
			'foreignKey' => 'agency_id',
		]);

		$this->hasMany('OutgoingDocuments', [
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

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'AuditReviewers',
				'OutgoingProfile',
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
			'collectionClass' => AgenciesCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new AgencyListener()
		);
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
			->maxLength('division', 50)
			->allowEmptyString('division');

		$validator
			->maxLength('email', 50)
			->allowEmptyString('email');

		$validator
			->allowEmptyString('phone');

		$validator
			->add('phone', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a phone number in (###) ###-#### format.'),
			])
			->maxLength('phone', 50)
			->allowEmptyString('phone');

		$validator
			->maxLength('fax', 50)
			->allowEmptyString('fax');

		$validator
			->maxLength('website', 80)
			->allowEmptyString('website');

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
			->add('state', 'custom', [
				'rule' => function ($value, $context) {
					$stateValues = array_keys(LocationUtility::states());

					return in_array($value, $stateValues);
				},
				'message' => __('The state is not valid'),
			])
			->maxLength('state', 2)
			->allowEmptyString('state', 'create');

		$validator
			->maxLength('zip', 20)
			->allowEmptyString('zip');

		$validator
			->maxLength('contact_name', 150)
			->allowEmptyString('contact_name');

		$validator
			->maxLength('contact_title', 50)
			->allowEmptyString('contact_title');

		$validator
			->maxLength('contact_email', 50)
			->allowEmptyString('contact_email');

		$validator
			->maxLength('contact_phone', 50)
			->allowEmptyString('contact_phone');

		$validator
			->maxLength('contact_fax', 50)
			->allowEmptyString('contact_fax');

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
