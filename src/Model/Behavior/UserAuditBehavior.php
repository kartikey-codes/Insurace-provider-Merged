<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use App\Model\Entity\User;
use App\Model\Table\UsersTable;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\RulesChecker;
use Cake\Routing\Router;
use Cake\Validation\Validator;

/**
 * User Audit Behavior
 *
 * Handles storing the user ID that created, modified, or deleted a row in the database.
 *
 * Load -AFTER- muffin/trash or else deleted_by rule will fail with user_id does not exist.
 */
class UserAuditBehavior extends Behavior
{
	/**
	 * @var ?\App\Model\Entity\User
	 */
	protected ?User $user = null;

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'fields' => [
			'created' => 'created_by',
			'modified' => 'modified_by',
			'deleted' => 'deleted_by',
		],
	];

	/**
	 * Set up the behavior
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$request = Router::getRequest();

		if ($request !== null) {
			$identity = $request->getAttribute('identity');

			if ($identity !== null) {
				$this->user = $identity->getOriginalData();
			}
		}

		if ($this->auditsCreated()) {
			$this->table()->belongsTo('CreatedByUser', [
				'foreignKey' => $this->getConfig('fields.created'),
				'className' => UsersTable::class,
				'finder' => 'limited',
			]);
		}

		if ($this->auditsModified()) {
			$this->table()->belongsTo('ModifiedByUser', [
				'foreignKey' => $this->getConfig('fields.modified'),
				'className' => UsersTable::class,
				'finder' => 'limited',
			]);
		}

		if ($this->auditsDeleted()) {
			$this->table()->belongsTo('DeletedByUser', [
				'foreignKey' => $this->getConfig('fields.deleted'),
				'className' => UsersTable::class,
				'finder' => 'limited',
			]);
		}
	}

	/**
	 * Get the current user
	 *
	 * @return \App\Model\Entity\User|null
	 */
	public function getUser(): ?User
	{
		if (empty($this->user)) {
			return null;
		}

		return $this->user;
	}

	/**
	 * Get the user ID from the session
	 * Allowed to return empty to avoid problems with CLI tools
	 *
	 * @return int|null
	 */
	public function getUserId(): ?int
	{
		$user = $this->getUser();

		if (empty($user) || empty($user->id)) {
			return null;
		}

		return $user->id;
	}

	/**
	 * Return whether attached model stores which user created it
	 *
	 * @return bool
	 */
	protected function auditsCreated(): bool
	{
		return $this->table()->hasField(
			$this->getConfig('fields.created')
		);
	}

	/**
	 * Return whether attached model stores which user last modified it
	 *
	 * @return bool
	 */
	protected function auditsModified(): bool
	{
		return $this->table()->hasField(
			$this->getConfig('fields.modified')
		);
	}

	/**
	 * Return whether attached model stores which user soft-deleted it
	 *
	 * @return bool
	 */
	protected function auditsDeleted(): bool
	{
		return $this->table()->hasField(
			$this->getConfig('fields.deleted')
		);
	}

	/**
	 * BuildValidator Model Callback
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Validation\Validator $validator
	 * @param mixed $name
	 * @return \Cake\Validation\Validator
	 */
	public function buildValidator(EventInterface $event, Validator $validator, mixed $name): Validator
	{
		if ($this->auditsCreated()) {
			$validator
				->add($this->getConfig('fields.created'), 'valid', ['rule' => 'numeric'])
				->allowEmptyString($this->getConfig('fields.created'));
		}

		if ($this->auditsModified()) {
			$validator
				->add($this->getConfig('fields.modified'), 'valid', ['rule' => 'numeric'])
				->allowEmptyString($this->getConfig('fields.modified'));
		}

		if ($this->auditsDeleted()) {
			$validator
				->add($this->getConfig('fields.deleted'), 'valid', ['rule' => 'numeric'])
				->allowEmptyString($this->getConfig('fields.deleted'));
		}

		return $validator;
	}

	/**
	 * BuildRules Model Callback
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\RulesChecker $rules
	 * @return \Cake\ORM\RulesChecker
	 */
	public function buildRules(EventInterface $event, RulesChecker $rules): RulesChecker
	{
		if ($this->auditsCreated()) {
			$rules->add($rules->existsIn([
				$this->getConfig('fields.created'),
			], 'CreatedByUser'));
		}

		if ($this->auditsModified()) {
			$rules->add($rules->existsIn([
				$this->getConfig('fields.modified'),
			], 'ModifiedByUser'));
		}

		if ($this->auditsDeleted()) {
			$rules->add($rules->existsIn($this->getConfig('fields.deleted'), 'DeletedByUser'));
		}

		return $rules;
	}

	/**
	 * BeforeSave Model Callback
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\Entity $entity
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity): void
	{
		$config = $this->getConfig();
		$userId = $this->getUserId();

		if (!empty($userId)) {
			// New
			if ($entity->isNew()) {
				$entity->set($config['fields']['created'], $userId);
			}

			$entity->set($config['fields']['modified'], $userId);
		}
	}

	/**
	 * BeforeDelete Model Callback
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\Entity $entity
	 * @return void
	 */
	public function beforeDelete(EventInterface $event, EntityInterface $entity): void
	{
		$config = $this->getConfig();
		$userId = $this->getUserId();

		if (!empty($userId)) {
			$entity->set($config['fields']['deleted'], $userId);
		}
	}
}
