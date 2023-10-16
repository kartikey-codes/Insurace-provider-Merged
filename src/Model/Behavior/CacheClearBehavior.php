<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;

/**
 * Cache Clear behavior
 *
 * Handles clearing caches after an entity has been updated
 */
class CacheClearBehavior extends Behavior
{
	protected $_defaultConfig = [
		'key' => 'entity',
		'stores' => [],
	];

	/**
	 * Set up the behavior
	 */
	public function initialize(array $config): void
	{
	}

	/**
	 * Clear caches after save committed
	 *
	 * @param \Cake\Event\Event $event Event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterSaveCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		$this->clearCaches();
	}

	/**
	 * Clear caches after save
	 *
	 * @param \Cake\Event\Event $event Event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		$this->clearCaches();
	}

	/**
	 * Clear caches after delete
	 *
	 * @param \Cake\Event\Event $event Event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		$this->clearCaches();
	}

	/**
	 * Clear caches after delete committed
	 *
	 * @param \Cake\Event\Event $event Event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function afterDeleteCommit(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		$this->clearCaches();
	}

	/**
	 * Clear caches
	 *
	 * @return void
	 */
	protected function clearCaches(): void
	{
		$key = $this->getConfig('key');
		$stores = $this->getConfig('stores');

		foreach ($stores as $store) {
			Cache::delete($key, $store);
		}
	}
}
