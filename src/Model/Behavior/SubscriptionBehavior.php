<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Subscription behavior
 *
 * Adds methods for managing subscription status.
 */
class SubscriptionBehavior extends Behavior
{
	/**
     * @var array
     */
	protected $_defaultConfig = [
		'active_field' => 'subscription_active',
	];

	/**
	 * Activate Subscription
	 *
	 * @return bool
	 * @throws \App\Model\Behavior\PeristenceFailedException
	 */
	public function activateSubscription(EntityInterface $entity): EntityInterface
	{
		$field = $this->getSubscriptionActiveBooleanField();
		$entity->set($field, true);

		return $this->table()->saveOrFail($entity);
	}

	/**
	 * Dectivate Subscription
	 *
	 * @param \App\Model\Behavior\Entity $entity
	 * @return \Cake\Datasource\EntityInterface
	 * @throws \App\Model\Behavior\PeristenceFailedException
	 */
	public function deactivateSubscription(EntityInterface $entity): EntityInterface
	{
		$field = $this->getSubscriptionActiveBooleanField();
		$entity->set($field, false);

		return $this->table()->saveOrFail($entity);
	}

	/**
	 * Find Clients Subscribed
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \App\Model\Behavior\Cake\ORM\Query
	 */
	public function findSubscribed(Query $query, array $options): Query
	{
		$field = $this->getSubscriptionActiveBooleanField();

		return $query->where(function ($exp) use ($field, $options) {
			return $exp->eq($this->table()->aliasField($field), true);
		});
	}

	/**
	 * Find Clients Not Subscribed
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \App\Model\Behavior\Cake\ORM\Query
	 */
	public function findNotSubscribed(Query $query, array $options): Query
	{
		$field = $this->getSubscriptionActiveBooleanField();

		return $query->where(function ($exp) use ($field, $options) {
			return $exp->eq($this->table()->aliasField($field), false);
		});
	}

	/**
	 * Return the aliased boolean field name for this table
	 *
	 * @return string
	 */
	private function getSubscriptionActiveBooleanField(): string
	{
		return $this->getConfig('active_field');
	}

	/**
	 * Disable / turn off subscription (in our database)
	 *
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param array $options
	 * @return \Cake\Datasource\EntityInterface
	 */
	public function disableSubscription(EntityInterface $entity, array $options = []): EntityInterface
	{
		$entity->set([
			$this->getSubscriptionActiveBooleanField() => false,
		], [
			'guard' => false,
		]);

		return $this->table()->saveOrFail($entity, $options);
	}

	/**
	 * Clear all subscription data
	 *
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param array $options
	 * @return \Cake\Datasource\EntityInterface
	 */
	public function clearSubscription(EntityInterface $entity, array $options = []): EntityInterface
	{
		$entity->set([
			$this->getSubscriptionActiveBooleanField() => false,
			// Should probably move these to behavior configuration
			'payment_provider_customer_id' => null,
			'payment_provider_name' => null,
			'payment_provider_subscription_id' => null,
			'subscription_product_id' => null,
		], [
			'guard' => false,
		]);

		return $this->table()->saveOrFail($entity, $options);
	}
}
