<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\ClientListener;
use App\Lib\LocationUtility\LocationUtility;
use App\Model\Entity\Client;
use App\Model\Filter\ClientsCollection;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use RuntimeException;

/**
 * Clients Model
 */
class ClientsTable extends Table
{
	use LocatorAwareTrait;

	/**
	 * Statuses available for a client to exist in
	 *
	 * @todo Move to enum when PHP 8.1 support is better
	 * @return array
	 */
	public static function statuses(): array
	{
		return [
			'Active' => 'Active',
			'Inactive' => 'Inactive',
			'Pending' => 'Pending',
			'On-Hold' => 'On-Hold',
		];
	}

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('clients');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->hasMany('Cases', [
			'foreignKey' => 'client_id',
		]);

		$this->hasMany('ClientEmployees', [
			'foreignKey' => 'client_id',
		]);

		$this->hasMany('Facilities', [
			'foreignKey' => 'client_id',
		]);

		$this->hasMany('Users', [
			'className' => 'Users',
			'foreignKey' => 'client_id',
		]);

		$this->hasMany('Patients', [
			'foreignKey' => 'client_id',
		]);

		$this->belongsTo('ClientTypes', [
			'foreignKey' => 'client_type_id',
		]);

		$this->belongsTo('Owner', [
			'className' => 'Users',
			'foreignKey' => 'owner_user_id',
		]);

		$this->hasOne('ClientSubscriptions', [
			'foreignKey' => 'client_id',
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
			'key' => 'Clients',
			'stores' => ['all', 'active'],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [],
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
			'collectionClass' => ClientsCollection::class,
		]);

		// Active
		$this->addBehavior('Subscription', [
			'active_field' => 'subscription_active',
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new ClientListener()
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
			->add('status', 'custom', [
				'rule' => function ($value, $context) {
					$statusValues = array_keys($this::statuses());

					return in_array($value, $statusValues);
				},
				'message' => __('The status is not valid'),
			])
			->requirePresence('status', 'create')
			->notEmptyString('status');

		$validator
			->add('email', 'valid', [
				'rule' => 'email',
				'message' => __('Must be a valid email address'),
			])
			->add('email', [
				'unique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => __('This email is already registered.'),
				],
			])
			->maxLength('email', 50)
			->requirePresence('email', 'create')
			->notEmptyString('email');

		$validator
			->add('phone', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a phone number in (###) ###-#### format.'),
			])
			->maxLength('phone', 50)
			->allowEmptyString('phone');

		$validator
			->add('fax', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a fax number in (###) ###-#### format.'),
			])
			->maxLength('fax', 50)
			->allowEmptyString('fax');

		$validator
			->add('contact_email', 'valid', [
				'rule' => 'email',
				'message' => __('Must be a valid email address'),
			])
			->maxLength('contact_email', 50)
			->allowEmptyString('contact_email');

		$validator
			->add('contact_phone', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a phone number in (###) ###-#### format.'),
			])
			->maxLength('contact_phone', 50)
			->allowEmptyString('contact_phone');

		$validator
			->add('contact_fax', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a fax number in (###) ###-#### format.'),
			])
			->maxLength('contact_fax', 50)
			->allowEmptyString('contact_fax');

		$validator
			->add('npi_number', [
				'unique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => __('This organization is already registered.'),
				],
			])
			->requirePresence('npi_number', 'create')
			->notEmptyString('npi_number');

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
		$checkOptions = [
			'skipTenantCheck' => true,
		];

		$rules->add($rules->isUnique(['name'], [
			'message' => 'Name is already registered',
			'skipTenantCheck' => true,
		]), 'uniqueName', $checkOptions);

		$rules->add($rules->isUnique(['email'], [
			'message' => 'Email address is already registered',
			'skipTenantCheck' => true,
		]), 'uniqueEmail', $checkOptions);

		$rules->add($rules->isUnique(['npi_number'], [
			'message' => 'NPI number is already registered',
			'skipTenantCheck' => true,
		]), 'uniqueNpi', $checkOptions);

		return $rules;
	}

	/**
	 * Before converted to entity
	 *
	 * @param \App\Model\Table\App\Model\Table\Event $event
	 * @param \ArrayObject $data
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
	{
		if (isset($data['phone'])) {
			$data['phone'] = $this->formatPhone($data['phone']);
		}

		if (isset($data['contact_phone'])) {
			$data['contact_phone'] = $this->formatPhone($data['contact_phone']);
		}

		if (isset($data['contact_fax'])) {
			$data['contact_fax'] = $this->formatPhone($data['contact_fax']);
		}
	}

	/**
	 * Logic to execute before saving
	 */
	public function beforeSave(EventInterface $event, Client $entity, ArrayObject $options): void
	{
		if ($entity->isDirty('licenses') && !$entity->isNew()) {
			// Updated Licenses Event
			$this->getEventManager()->dispatch(
				new Event('Model.Client.updatedLicenses', $this, [
					$entity,
					[
						// Options
						'original_licenses' => $entity->getOriginal('licenses'),
						'new_licenses' => $entity->get('licenses'),
					],
				])
			);
		}
	}

	/**
	 * Format phone number
	 *
	 * @param string $phone
	 * @return string
	 */
	public function formatPhone(string $phone): string
	{
		$phone = preg_replace('/[^0-9]*/', '', $phone);

		if (strlen($phone) == 7) {
			return preg_replace("/(\d{3})(\d{4})/", '$1-$2', $phone);
		} elseif (strlen($phone) == 10) {
			return preg_replace("/(\d{3})(\d{3})(\d{4})/", '($1) $2-$3', $phone);
		} else {
			return $phone;
		}
	}

	/**
	 * After Delete Event
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \App\Model\Table\App\Model\Table\ArrayObject $options
	 * @return void
	 */
	public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		//$this->fetchTable('FacilityTypes')->clearDataForClient($entity->id);
	}

	/**
	 * Find Clients By Status
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByStatus(Query $query, array $options): Query
	{
		if (empty($options['Status'])) {
			throw new RuntimeException(__('Status must be provided to search clients by.'));
		}

		return $query->where(function ($exp) use ($options) {
			return $exp->eq($this->aliasField('status'), $options['Status']);
		});
	}
}
