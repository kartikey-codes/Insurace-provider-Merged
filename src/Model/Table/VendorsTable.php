<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\VendorListener;
use App\Lib\LocationUtility\LocationUtility;
use App\Model\Entity\Appeal;
use App\Model\Filter\VendorsCollection;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use InvalidArgumentException;

/**
 * Vendors Model
 */
class VendorsTable extends Table
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
		$this->setTable('vendors');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsToMany('Specialties', [
			'through' => 'VendorSpecialties',
			'foreignKey' => 'vendor_id',
			'targetForeignKey' => 'specialty_id',
			'saveStrategy' => 'replace',
		]);

		$this->belongsTo('Owner', [
			'className' => 'Users',
			'foreignKey' => 'owner_user_id',
		]);

		$this->hasMany('Users', [
			'className' => 'Users',
			'foreignKey' => 'vendor_id',
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
			'key' => 'Vendors',
			'stores' => ['all', 'active'],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'Specialties' => [
					'finder' => 'ordered',
				],
				'Owner',
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
			'collectionClass' => VendorsCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new VendorListener()
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
			->requirePresence('name', 'create')
			->notEmptyString('name');

		$validator
			->add('state', 'custom', [
				'rule' => function ($value, $context) {
					$stateValues = array_keys(LocationUtility::states());

					return in_array($value, $stateValues);
				},
				'message' => __('The state is not valid'),
			])
			->allowEmptyString('state', 'create');

		$validator
			->add('phone', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a phone number in (###) ###-#### format.'),
			])
			->allowEmptyString('phone');

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

		return $rules;
	}

	/**
	 * Find Assignable To Appeal
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssignableToAppeal(Query $query, array $options): Query
	{
		if (empty($options['appeal'])) {
			throw new InvalidArgumentException(__('An appeal must be provided'));
		}

		if (!$options['appeal'] instanceof Appeal) {
			throw new InvalidArgumentException(__('A valid appeal must be provided'));
		}

		/**
		 * @var \App\Model\Entity\Appeal $appeal
		 */
		$appeal = $options['appeal'];

		/** @todo Use appeal to verify matching criteria (specialties, appeal_level, etc...) */

		return $query->where([
			$this->aliasField('active') => true,
		]);
	}

	/**
	 * Clear Appeal Queue
	 *
	 * @param int $id
	 * @return bool
	 */
	public function clearAssignedAppeals(int $id): bool
	{
		$appealsTable = $this->fetchTable('Appeals');

		$appeals = $appealsTable->find('assignedToVendor', [
			'id' => $id,
		]);

		foreach ($appeals as $appeal) {
			$appeal->set('assigned_to_vendor_id', null);

			$this->saveOrFail($appeal);
		}

		return true;
	}
}
