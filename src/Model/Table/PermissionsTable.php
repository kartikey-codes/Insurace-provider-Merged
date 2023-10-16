<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\PermissionsCollection;
use ArrayObject;
use Cake\Cache\Cache;
use Cake\Core\Exception\CakeException;
use Cake\Event\EventInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use InvalidArgumentException;

/**
 * Permissions Model
 *
 * @param \App\Model\Table\RolesTable $Roles
 */
class PermissionsTable extends Table
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
		$this->setTable('permissions');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsToMany('Roles', [
			'through' => 'RolesPermissions',
			'joinTable' => 'roles_permissions',
			'foreignKey' => 'permission_id',
			'targetForeignKey' => 'role_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'Permissions',
			'stores' => ['all'],
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				/**
				 * Belongs To Many
				 */
				'Roles' => [
					'Users',
				],
			],
		]);

		// Orderable
		$this->addBehavior('Orderable', [
			'default' => [
				$this->aliasField('controller') => 'asc',
				$this->aliasField('name') => 'asc',
				$this->aliasField('action') => 'asc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => PermissionsCollection::class,
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
			// ->add('controller', [
			// 	'inList' => [
			// 		'rule' => ['inList', array_keys($this->getControllers())],
			// 		'message' => __('Controller must be in the valid list of controllers.'),
			// 	],
			// ])
			->minLength('controller', 1)
			->maxLength('controller', 50)
			->requirePresence('controller', 'create')
			->notEmptyString('controller');

		$validator
			->minLength('action', 1)
			->maxLength('action', 50)
			->requirePresence('action', 'create')
			->notEmptyString('action');

		$validator
			->minLength('name', 2)
			->maxLength('name', 80)
			->requirePresence('name', 'create')
			->notEmptyString('name');


		$validator
			->maxLength('category', 40)
			->allowEmptyString('category');

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

	/**
	 * Data to adjust before being converted to an entity
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \ArrayObject $data
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
	{
		// Generate the key
		if (isset($data['controller']) && isset($data['action'])) {
			$data['key'] = strtolower($data['controller'] . '__' . $data['action']);
		}
	}

	/**
	 * After Save
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\Entity $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterSave(EventInterface $event, Entity $entity, ArrayObject $options): void
	{
		// Clear cached permissions
		Cache::clear('permissions');
	}

	/**
	 * List the available controllers for permissions
	 *
	 * @return array
	 */
	public function getControllers(): array
	{
		return $this->find()
			->select(['controller'])
			->distinct('controller')
			->toArray();
	}

	/**
	 * Find Permissions By Role
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query;
	 */
	public function findByRole(Query $query, array $options): Query
	{
		if (empty($options['role_id'])) {
			return false;
		}

		$query->distinct();

		$query->matching('Roles', function ($q) use ($options) {
			return $q->where([
				$this->Roles->aliasField('id') => $options['role_id'],
			]);
		});

		return $query;
	}

	/**
	 * Find Permissions By User
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByUser(Query $query, array $options): Query
	{
		if (empty($options['user_id'])) {
			throw new InvalidArgumentException(__("User_id option was not provided."));
		}

		$query->select([
			$this->aliasField('id'),
			$this->aliasField('controller'),
			$this->aliasField('action'),
			$this->aliasField('name'),
		]);

		$query->distinct([
			$this->aliasField('id'),
		]);

		$query->matching('Roles.Users', function ($q) use ($options) {
			return $q->distinct([
				$this->aliasField('id'),
			])->where([
				$this->Roles->Users->aliasField('id') => $options['user_id'],
			]);
		});

		return $query;
	}

	/**
	 * Map/Reduce Permissions to be grouped by Controller
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findGroupedByController(Query $query, array $options): Query
	{
		$query->mapReduce(
			// Mapper
			function ($permission, $key, $mapReduce): void {
				$mapReduce->emitIntermediate($permission, $permission->controller);
			},
			// Reducer
			function ($permissions, $controller, $mapReduce): void {
				$controller = Inflector::humanize($controller);
				$mapReduce->emit($permissions, $controller);
			}
		);

		return $query;
	}

	/**
	 * Map/Reduce Permissions to be grouped by controller containing just the actions as subkeys
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findGroupedSimple(Query $query, array $options): Query
	{
		$query->mapReduce(
			// Mapper
			function ($permission, $key, $mapReduce): void {
				$mapReduce->emitIntermediate($permission->action, $permission->controller);
			},
			// Reducer
			function ($permissions, $controller, $mapReduce): void {
				$controller = Inflector::humanize($controller);
				$mapReduce->emit($permissions, $controller);
			}
		);

		return $query;
	}

	/**
	 * Find Matching Request
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findMatchingRequest(Query $query, array $options): Query
	{
		if (!$options['request'] instanceof ServerRequest) {
			throw new CakeException(__('A valid Server Request must be passed as a parameter.'));
		}

		return $query->where(function ($exp) use ($options) {
			return $exp
				->eq($this->aliasField('controller'), $options['request']->getParam('controller'))
				->eq($this->aliasField('action'), $options['request']->getParam('action'));
		});
	}

	/**
	 * Find User Effective Permissions
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 * @throws \Cake\Core\Exception\CakeException
	 */
	public function findUserEffective(Query $query, array $options): Query
	{
		if (empty($options['user_id'])) {
			throw new CakeException(__('User ID not provided to find effective permissions.'));
		}

		/** @var bool $isAdmin */
		$isAdmin = $this->fetchTable('Users')->isAdmin($options['user_id']);

		// Admins have all permissions
		if ($isAdmin === true) {
			return $this->find('all');
		}

		return $this->find('byUser', [
			'user_id' => $options['user_id'],
		]);
	}
}
