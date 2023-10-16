<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Filter\RolesCollection;
use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use InvalidArgumentException;

/**
 * Roles Model
 */
class RolesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('roles');

		$this->setPrimaryKey('id');

		$this->setDisplayField('name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsToMany('Users', [
			'through' => 'UsersRoles',
			'joinTable' => 'users_roles',
			'foreignKey' => 'role_id',
			'targetForeignKey' => 'user_id',
		]);

		$this->belongsToMany('Permissions', [
			'through' => 'RolesPermissions',
			'joinTable' => 'roles_permissions',
			'foreignKey' => 'role_id',
			'targetForeignKey' => 'permission_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'Roles',
			'stores' => ['all'],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				/**
				 * Belongs To Many
				 */
				'Users',
				'Permissions',
			],
			'limitedFields' => [
				'id',
				'name',
				'created',
				'modified'
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
			'collectionClass' => RolesCollection::class,
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
			->minLength('name', 2, __('Name must be at least 2 characters'))
			->maxLength('name', 250, __('Name must be less than 250 characters'))
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

		return $rules;
	}

	/**
	 * After Save
	 *
	 * @return void
	 */
	public function afterSave(EventInterface $event, Entity $entity, ArrayObject $options): void
	{
		// Clear cached permissions
		Cache::clear('permissions');
	}

	/**
	 * Get All Records
	 *
	 * @return \Cake\Datasource\ResultSetInterface
	 */
	public function getAll(): ResultSetInterface
	{
		return $this->find('all')
			->find('ordered')
			->contain([
				'Permissions',
			])
			->all();
	}

	/**
	 * Find By Permission
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByPermission(Query $query, array $options): Query
	{
		if (empty($options['permission_id'])) {
			throw new InvalidArgumentException(__('A permission ID is required to find roles by permission'));
		}

		return $query->matching('Permissions', function ($q) use ($options) {
			return $q->where([
				$this->Permissions->aliasField('id') => $options['permission_id'],
			]);
		});
	}

	/**
	 * Find By User
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByUser(Query $query, array $options): Query
	{
		if (empty($options['user_id'])) {
			throw new InvalidArgumentException(__('A user ID is required to find roles by user'));
		}

		return $query->matching('Users', function ($q) use ($options) {
			return $q->where([
				$this->Users->aliasField('id') => $options['user_id'],
			]);
		});
	}
}
