<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Core\Exception\CakeException;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use InvalidArgumentException;

/**
 * Assignable Behavior
 *
 * Handles assigning models to users
 */
class AssignableBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'fields' => [
			'timestamp' => 'assigned',
			'user_id' => 'assigned_to',
		],
		'userModel' => 'Users',
		'implementedFinders' => [
			'assigned' => 'findAssigned',
			'assignedTo' => 'findAssignedTo',
			'unassigned' => 'findUnassigned',
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
		// Check for the user_id field (required)
		if (!$this->table()->hasField($this->getConfig('fields.user_id'))) {
			throw new CakeException(__(
				'Table "{0}" does not have a assigned_to field.',
				$this->table()
			));
		}

		$this->table()->belongsTo('AssignedToUser', [
			'foreignKey' => $this->getConfig('fields.user_id'),
			'className' => $this->getConfig('userModel'),
		]);
	}

	/**
	 * Logic to execute before saving
	 *
	 * @param \Cake\Event\Event $event
	 * @param \Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		// Set the assigned timestamp
		if ($this->table()->hasField($this->getConfig('fields.timestamp'))) {
			// Only update timestamp if our password is dirty
			if ($entity->isDirty($this->getConfig('fields.user_id'))) {
				$entity->set(
					$this->getConfig('fields.timestamp'),
					new FrozenTime('now')
				);
			}
		}
	}

	/**
	 * Find Assigned
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssigned(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNotNull(
				$this->table()->aliasField($this->getConfig('fields.user_id'))
			);
		});
	}

	/**
	 * Find Assigned To
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAssignedTo(Query $query, array $options): Query
	{
		if (!isset($options['UserId'])) {
			throw new InvalidArgumentException(__('A UserId parameter must be provided to find assigned appeals'));
		}

		return $query->where(function (QueryExpression $exp, Query $q) use ($options) {
			return $exp->eq(
				$this->table()->aliasField($this->getConfig('fields.user_id')),
				$options['UserId']
			);
		});
	}

	/**
	 * Find Unssigned
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findUnassigned(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->isNull(
				$this->table()->aliasField($this->getConfig('fields.user_id'))
			);
		});
	}
}
