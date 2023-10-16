<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Client;
use App\Subscription\ClientSubscriptionCancelResponse;
use App\Subscription\ClientSubscriptionResponse;

/**
 * Subscription Service Provider Interface
 */
interface SubscriptionServiceInterface
{
	/**
	 * @var string
	 */
	public const DRIVER_NULL = 'null';

	/**
	 * @var string
	 */
	public const DRIVER_STRIPE = 'stripe';

	/**
	 * Determine if the subscription functionality should be enabled
	 *
	 * @return bool
	 */
	public function isEnabled(): bool;

	/**
	 * Determine if an active subscription is required for a client
	 *
	 * @return bool
	 */
	public function isRequired(): bool;

	/**
	 * Returns list of products available to a client
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return array<\App\Subscription\ClientProductResponse>
	 */
	public function listClientProducts(Client $client): array;

	/**
	 * Get or create a customer associated with the payment provider and return a
	 * response with an ID to refrence the customer later.
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return string
	 */
	public function getClientCustomerId(Client $client): string;

	/**
	 * Return whether an existing subscription exists for a client or not
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return bool
	 */
	public function clientHasSubscription(Client $client): bool;

	/**
	 * Create a new subscription from supplied data and return info needed to
	 * store how to charge the customer later.
	 *
	 * @param \App\Model\Entity\Client $client
	 * @param array $data
	 * @return \App\Subscription\ClientSubscriptionResponse
	 */
	public function createClientSubscription(Client $client, array $data): ClientSubscriptionResponse;

	/**
	 * Return a specific subscription for a client
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return \App\Subscription\ClientSubscriptionResponse
	 */
	public function getClientSubscription(Client $client): ClientSubscriptionResponse;

	/**
	 * Cancel a client's subscription
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return \App\Subscription\ClientSubscriptionResponse
	 */
	public function cancelClientSubscription(Client $client): ClientSubscriptionCancelResponse;

	/**
	 * Estimate a client's billed price
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return float
	 */
	public function estimateClientPricing(Client $client): float;

	/**
	 * Update license quantity for a client's subscription
	 *
	 * @param \App\Model\Entity\Client $client
	 * @param int $value
	 * @return bool
	 */
	public function updateClientSubscriptionQuantity(Client $client, int $value): bool;
}
