<?php

declare(strict_types=1);

namespace App\Service\Subscription;

use App\Exception\ClientSubscriptionDoesNotExistException;
use App\Exception\ClientSubscriptionsUnavailableException;
use App\Exception\SubscriptionServiceCreateCustomerException;
use App\Exception\SubscriptionServiceInvalidDataException;
use App\Model\Entity\Client;
use App\Service\SubscriptionServiceInterface;
use App\Subscription\ClientCustomerResponse;
use App\Subscription\ClientProductResponse;
use App\Subscription\ClientSubscriptionCancelResponse;
use App\Subscription\ClientSubscriptionResponse;
use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Stripe\Product as StripeProduct;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;

/**
 * Stripe Subscription Provider
 */
class StripeSubscriptionService implements SubscriptionServiceInterface
{
	use LocatorAwareTrait;

	/**
	 * @var string
	 */
	public const PROVIDER_NAME = 'stripe';

	/**
	 * @var array
	 */
	private array $config;

	/**
	 * @var \Stripe\StripeClient
	 */
	private StripeClient $gateway;

	/**
	 * @var \Cake\ORM\Table
	 */
	private Table $clients;

	/**
	 * Config value
	 *
	 * @var bool
	 */
	protected bool $isEnabled = true;

	/**
	 * Config value
	 *
	 * @var bool
	 */
	protected bool $isRequired = true;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->config = Configure::read('Subscriptions.Stripe');
		$this->isEnabled = boolval(Configure::read('Subscriptions.enabled'));
		$this->isRequired = boolval(Configure::read('Subscriptions.required'));
		$this->gateway = new StripeClient($this->config['secretKey']);
		$this->clients = $this->fetchTable('Clients');
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
		$products = $this->gateway->products->all([
			'limit' => 10,
		]);

		if ($products->isEmpty() || empty($products->data)) {
			throw new ClientSubscriptionsUnavailableException([]);
		}

		return array_map(function ($product) use ($client) {
			return $this->mapProductResult($product, $client);
		}, $products->data);
	}

	/**
	 * Map Stripe's product object into our app model
	 *
	 * @param \Stripe\Product $product
	 * @param \App\Model\Entity\Client $client
	 * @return \App\Subscription\ClientProductResponse
	 */
	private function mapProductResult(StripeProduct $product, Client $client): ClientProductResponse
	{
		$object = new ClientProductResponse();

		$prices = $this->gateway->prices->all([
			'product' => $product->id,
		]);

		/**
		 * @var \Stripe\Price $price
		 */
		$price = $prices->first();

		$object->provider = self::PROVIDER_NAME;
		$object->id = $price->id;
		$object->name = $product->name;
		$object->description = $product->description;
		$object->active = $product->active;
		$object->recurringInterval = $price->recurring->interval;

		// Returns null for tiered pricing... :(
		$object->recurringPrice = !empty($price->unit_amount_decimal) ? $price->unit_amount_decimal / 100 : 0.00;

		$object->metaData = [
			'product' => $product->toArray(),
			'prices' => $prices->toArray(),
		];

		return $object;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientCustomerId(Client $client): string
	{
		$customer = $this->findOrCreateCustomer($client);

		return $customer->id;
	}

	/**
	 * Find or create a customer record at Stripe based on our app Client model
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return \App\Subscription\ClientCustomerResponse
	 * @throws \Stripe\Exception\ApiErrorException
	 * @throws \App\Exception\SubscriptionServiceCreateCustomerException
	 * @throws \InvalidArgumentException
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	private function findOrCreateCustomer(Client $client): ClientCustomerResponse
	{
		// Check if customer exists at Stripe already
		$existingCustomers = $this->gateway->customers->all([
			'email' => $client->email,
			'limit' => 1,
		]);

		// Found existing customer at Stripe
		if ($existingCustomers->isEmpty() == false) {
			/**
			 * @var \Stripe\Customer $existingCustomer
			 */
			$existingCustomer = $existingCustomers->first();

			$customerId = $existingCustomer->id;
			$response = new ClientCustomerResponse();
			$response->provider = self::PROVIDER_NAME;
			$response->id = $customerId;
			$response->metaData = [
				'customer' => $existingCustomer,
			];
		} else {
			$response = $this->createClientCustomer($client);
			$customerId = $response->id;
		}

		// Update client with existing customer ID
		$client->payment_provider_customer_id = $customerId;
		$this->clients->saveOrFail($client);

		return $response;
	}

	/**
	 * Create a customer associated with the payment provider and return a
	 * response with an ID to refrence the customer later.
	 *
	 * @return \App\Subscription\ClientCustomerResponse
	 */
	private function createClientCustomer(Client $client): ClientCustomerResponse
	{
		$object = new ClientCustomerResponse();

		$request = [
			'address' => [
				'city' => $client->city,
				'country' => 'US',
				'line1' => $client->street_address_1,
				'line2' => $client->street_address_2,
				'postal_code' => $client->zip,
				'state' => $client->state,
			],
			'name' => $client->name,
			'phone' => $client->phone,
			'email' => $client->email,
			'metadata' => [
				'client_id' => $client->id,
				'client_name' => $client->name,
				'client_email' => $client->email,
			],
		];

		/**
		 * @var \Stripe\Customer $response
		 */
		$response = $this->gateway->customers->create($request);

		if (empty($response->id)) {
			throw new SubscriptionServiceCreateCustomerException();
		}

		$object->provider = self::PROVIDER_NAME;
		$object->id = $response->id;
		$object->metaData = $response->toArray();

		return $object;
	}

	/**
	 * @inheritDoc
	 */
	public function createClientSubscription(Client $client, array $data): ClientSubscriptionResponse
	{
		/**
		 * @see https://stripe.com/docs/api/subscriptions/create
		 */

		$customerId = $client->payment_provider_customer_id;
		$productId = $client->subscription_product_id;
		$quantity = (int)$client->licenses;

		if (empty($quantity)) {
			throw new SubscriptionServiceInvalidDataException([
				'field' => 'licenses',
			]);
		}

		if (empty($customerId)) {
			throw new SubscriptionServiceInvalidDataException([
				'field' => 'payment_provider_customer_id',
			]);
		}

		if (empty($productId)) {
			throw new SubscriptionServiceInvalidDataException([
				'field' => 'subscription_product_id',
			]);
		}

		$this->gateway->subscriptions->create([
			'customer' => $customerId,
			'items' => [
				[
					'price' => $productId, // Stripe uses 'Prices' object
					'quantity' => $quantity,
				],
			],
			'payment_behavior' => 'default_incomplete',
			'expand' => ['latest_invoice.payment_intent'],
		]);

		return $this->getClientSubscription($client);
	}

	/**
	 * @inheritDoc
	 */
	public function clientHasSubscription(Client $client): bool
	{
		$customerId = $this->getClientCustomerId($client);

		$subscription = $this->gateway->subscriptions->all([
			'customer' => $customerId,
			'expand' => ['data.latest_invoice.payment_intent'],
		]);

		return $subscription->isEmpty() ? false : true;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientSubscription(Client $client): ClientSubscriptionResponse
	{
		$customerId = $this->getClientCustomerId($client);

		if (empty($customerId)) {
			throw new SubscriptionServiceInvalidDataException([
				'field' => 'payment_provider_customer_id',
			]);
		}

		$subscription = $this->gateway->subscriptions->all([
			'customer' => $customerId,
			'expand' => ['data.latest_invoice.payment_intent'],
		]);

		// Abort if client does not have an existing subscription
		if ($subscription->isEmpty()) {
			throw new ClientSubscriptionDoesNotExistException([]);
		}

		/**
		 * @var \Stripe\Subscription $firstSubscription
		 */
		$firstSubscription = $subscription->first();

		return $this->mapSubscriptionResult($firstSubscription);
	}

	/**
	 * Map Stripe's product object into our app model
	 *
	 * @param \Stripe\Product $subscription
	 * @return \App\Subscription\ClientSubscriptionResponse
	 */
	private function mapSubscriptionResult(StripeSubscription $subscription): ClientSubscriptionResponse
	{
		$object = new ClientSubscriptionResponse();

		$subscriptionProduct = $subscription->items->first();
		$product = $this->gateway->products->retrieve($subscriptionProduct->price->product);
		$prices = $this->gateway->prices->all(['product' => $product->id]);

		/**
		 * @var \Stripe\Price $price
		 */
		$price = $prices->first();

		$object->provider = self::PROVIDER_NAME;
		$object->id = $subscription->id;
		$object->name = $product->name;
		$object->description = $product->description;
		$object->active = $subscription->status == 'active' ? true : false;
		$object->periodEnd = $subscription->current_period_end;
		$object->periodStart = $subscription->current_period_start;
		$object->recurringInterval = $price->recurring->interval;
		$object->recurringPrice = $price->unit_amount_decimal / 100;
		$object->customerId = $subscription->customer;
		$object->productId = $subscription->items->first()->plan->product;
		$object->priceId = $price->id;
		$object->status = $subscription->status;
		$object->clientSecret = $subscription->latest_invoice->payment_intent->client_secret;

		$object->metaData = [
			'product' => $product->toArray(),
			'prices' => $prices->toArray(),
			'subscription' => $subscription->toArray(),
		];

		return $object;
	}

	/**
	 * @inheritDoc
	 */
	public function cancelClientSubscription(Client $client): ClientSubscriptionCancelResponse
	{
		$cancellation = $this->gateway->subscriptions->cancel($client->payment_provider_subscription_id, [
			'invoice_now' => true,
			'prorate' => true,
		]);

		$response = new ClientSubscriptionCancelResponse();

		$response->provider = self::PROVIDER_NAME;
		$response->id = $cancellation->id;
		$response->cancelled = $cancellation->status == 'canceled';
		$response->metaData = $cancellation->toArray();

		return $response;
	}

	/**
	 * @inheritDoc
	 */
	public function estimateClientPricing(Client $client): float
	{
		$invoice = $this->gateway->invoices->upcoming([
			'customer' => $client->payment_provider_customer_id,
			'subscription_items' => [
				[
					'price' => $client->subscription_product_id,
					'quantity' => $client->licenses,
				],
			],
		]);

		return $invoice->total / 100;
	}

	/**
	 * @inheritDoc
	 */
	public function updateClientSubscriptionQuantity(Client $client, int $value): bool
	{
		$response = $this->gateway->subscriptions->update(
			$client->payment_provider_subscription_id,
			[
				// 'items' => [
				// 	[
				// 		'id' => $client->subscription_product_id,
				// 		'quantity' => $value
				// 	]
				// ]
				'quantity' => $value,
			]
		);

		return true;
	}
}
