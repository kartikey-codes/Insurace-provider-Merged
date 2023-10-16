<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Exception\ClientSubscriptionUpdateException;
use App\Lib\TenantUtility\TenantUtility;
use App\Model\Entity\Client;
use App\Model\Table\ClientsTable;
use App\Service\LicenseServiceInterface;
use App\Service\SubscriptionServiceInterface;
use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Client Subscriptions Controller
 *
 * @property \App\Model\Table\Clients $Clients
 */
class SubscriptionsController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var int|null
	 */
	private $clientId;

	/**
	 * @var \App\Model\Entity\Client
	 */
	private $client;

	/**
	 * @var \App\Model\Table\ClientsTable
	 */
	public ClientsTable $Clients;

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		$this->Clients = $this->fetchTable('Clients');
		$this->getClient();
	}

	/**
	 * Get the Client entity from the request
	 *
	 * @return \App\Model\Entity\Client
	 */
	private function getClient(): Client
	{
		$this->clientId = TenantUtility::getTenantIdFromRequest();
		$this->client = $this->Clients->get($this->clientId);

		return $this->client;
	}

	/**
	 * List Subscriptions
	 *
	 * @return void
	 */
	public function index(SubscriptionServiceInterface $subscriptionService, LicenseServiceInterface $licenseService): void
	{
		$hasSubscription = $subscriptionService->clientHasSubscription($this->client);
		$subscription = null;

		if ($hasSubscription) {
			$subscription = $subscriptionService->getClientSubscription($this->client);
			$subscription->metaData = []; // Hide raw response
		}

		$data = [
			'client_name' => $this->client->name,
			'is_enabled' => $subscriptionService->isEnabled(),
			'is_required' => $subscriptionService->isRequired(),
			'licenses' => $licenseService->getClientAvailableLicenses($this->client),
			'subscription_active' => $this->client->subscription_active,
			'provider_customer_id' => $this->client->payment_provider_customer_id,
			'provider_subscription_id' => $this->client->payment_provider_subscription_id,
			'provider_subscription_exists' => $hasSubscription,
			'details' => $subscription,
		];

		$this->set(compact('data'));
	}

	/**
	 * Update Subscription
	 *
	 * @return void
	 */
	public function update(SubscriptionServiceInterface $subscriptionService): void
	{
		$this->request->allowMethod('post');

		// New license quantity
		$quantity = (int)$this->getRequest()->getData('quantity');

		if ($quantity < 1) {
			throw new ClientSubscriptionUpdateException(__('At least one license is required.'));
		}

		$max = Configure::read('Subscriptions.maxLicenses');
		if ($quantity >= $max) {
			throw new ClientSubscriptionUpdateException(__('Please contact us if you need more than {0} licenses.', $max));
		}

		// Attempt updating subscription
		$success = $subscriptionService->updateClientSubscriptionQuantity($this->client, $quantity);

		// Update client record in database
		if ($success) {
			$client = $this->client;
			$client->set('licenses', $quantity);

			$this->Clients->saveOrFail($client, [
				'skipTenantCheck' => true,
			]);
		}

		// Send back information
		$data = [
			'success' => $success,
			'quantity' => $quantity,
		];

		$this->set(compact('data'));
	}

	/**
	 * Cancel Subscription
	 *
	 * @return void
	 */
	public function cancel(SubscriptionServiceInterface $subscriptionService): void
	{
		$this->request->allowMethod('post');

		// Try to cancel subscription, throws exception if not
		$data = $subscriptionService->cancelClientSubscription($this->client);

		// Set client's subscription_active to false
		$this->Clients->disableSubscription($this->client);

		$this->set(compact('data'));
	}
}
