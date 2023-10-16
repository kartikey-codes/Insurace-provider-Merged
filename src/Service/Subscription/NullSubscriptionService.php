<?php

declare(strict_types=1);

namespace App\Service\Subscription;

use App\Model\Entity\Client;
use App\Service\SubscriptionServiceInterface;
use App\Subscription\ClientProductResponse;
use App\Subscription\ClientSubscriptionCancelResponse;
use App\Subscription\ClientSubscriptionResponse;
use Cake\Core\Configure;

/**
 * Null Subscription Provider
 *
 * Used for development to bypass contacting a subscription API and allow fake subscriptions for testing.
 */
class NullSubscriptionService implements SubscriptionServiceInterface
{
	/**
	 * @var string
	 */
	public const PROVIDER_NAME = 'null';

	/**
	 * Config value
	 *
	 * @var bool
	 */
	protected bool $isEnabled;

	/**
	 * Config value
	 *
	 * @var bool
	 */
	protected bool $isRequired;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->isEnabled = boolval(Configure::read('Subscriptions.enabled'));
		$this->isRequired = boolval(Configure::read('Subscriptions.required'));
	}

	/**
	 * Create a fake product (subscription plan)
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return \App\Subscription\ClientProductResponse
	 */
	private function mockProduct(Client $client): ClientProductResponse
	{
		$product = new ClientProductResponse();
		$product->provider = self::PROVIDER_NAME;
		$product->id = 'test';
		$product->name = 'Development';
		$product->description = 'This plan is used for development and costs nothing.';
		$product->active = true;
		$product->recurringInterval = 'month';
		$product->recurringPrice = 0;

		return $product;
	}

	/**
	 * Create a fake subscription
	 *
	 * @return \App\Subscription\ClientSubscriptionResponse
	 */
	private function mockSubscription(Client $client, ClientProductResponse $product): ClientSubscriptionResponse
	{
		$subscription = new ClientSubscriptionResponse();

		$subscription->provider = self::PROVIDER_NAME;
		$subscription->id = 'null_sub_' . $client->id;
		$subscription->name = $product->name;
		$subscription->description = $product->description;
		$subscription->active = true;
		$subscription->periodEnd = time() + (30 * 24 * 60 * 60);
		$subscription->periodStart = time();
		$subscription->recurringInterval = 'month';
		$subscription->recurringPrice = 0;

		return $subscription;
	}

	/**
	 * @inheritDoc
	 */
	public function isEnabled(): bool
	{
		return $this->isEnabled;
	}

	/**
	 * @inheritDoc
	 */
	public function isRequired(): bool
	{
		return $this->isRequired;
	}

	/**
	 * @inheritDoc
	 */
	public function listClientProducts(Client $client): array
	{
		$product = $this->mockProduct($client);

		return [$product];
	}

	/**
	 * @inheritDoc
	 */
	public function getClientCustomerId(Client $client): string
	{
		return 'customer_' . $client->id;
	}

	/**
	 * @inheritDoc
	 */
	public function createClientSubscription(Client $client, array $data): ClientSubscriptionResponse
	{
		$product = $this->mockProduct($client);
		$subscription = $this->mockSubscription($client, $product);

		return $subscription;
	}

	/**
	 * @inheritDoc
	 */
	public function clientHasSubscription(Client $client): bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientSubscription(Client $client): ClientSubscriptionResponse
	{
		$product = $this->mockProduct($client);
		$subscription = $this->mockSubscription($client, $product);

		return $subscription;
	}

	/**
	 * @inheritDoc
	 */
	public function cancelClientSubscription(Client $client): ClientSubscriptionCancelResponse
	{
		$response = new ClientSubscriptionCancelResponse();

		$response->provider = self::PROVIDER_NAME;
		$response->id = $client->payment_provider_subscription_id ?: '';
		$response->cancelled = true;

		return $response;
	}

	/**
	 * @inheritDoc
	 */
	public function estimateClientPricing(Client $client): float
	{
		return 0.00;
	}

	/**
	 * @inheritDoc
	 */
	public function updateClientSubscriptionQuantity(Client $client, int $value): bool
	{
		return true;
	}
}
