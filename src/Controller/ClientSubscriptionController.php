<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ClientSubscriptionsUnavailableException;
use App\Exception\SubscriptionServiceInvalidDriverException;
use App\Form\DemoPaymentForm;
use App\Form\NullPaymentForm;
use App\Form\StripePaymentForm;
use App\Form\SubscriptionProductForm;
use App\Lib\TenantUtility\TenantUtility;
use App\Model\Table\ClientsTable;
use App\Service\SubscriptionServiceInterface;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Log\Log;

/**
 * Client Subscription Controller
 *
 * Used for capturing user payment/subscription before allowing into app.
 *
 * @property \App\Model\Table\ClientsTable $Clients
 * @property \App\Controller\Component\LogComponent $Log
 */
class ClientSubscriptionController extends AppController
{
	/**
	 * @var \App\Model\Table\ClientsTable
	 */
	public ClientsTable $Clients;

	/** @var string */
	public const PAYMENT_SUCCESS = 'Your subscription has been created. Thank you!';

	/** @var string */
	public const SUBSCRIPTION_ERROR = 'Unable to confirm plan selection. Please check for any errors.';

	/** @var string */
	public const PAYMENT_ERROR = 'Unable to verify payment. Please check for any errors.';

	/** @var string */
	public const POST_PAYMENT_ERROR = 'Unable to verify subscription status with provider. Please contact us.';

	/** @var string */
	public const DRIVER_NULL = 'null';

	/** @var string */
	public const DRIVER_STRIPE = 'stripe';

	/**
	 * @var string
	 */
	public string $driver = '';

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

		$this->driver = Configure::read('Subscriptions.driver');

		$this->Clients = $this->fetchTable('Clients');
		$this->loadComponent('Log');
	}

	/**
	 * Before filter callback.
	 * Executes before every request.
	 *
	 * @param \Cake\Event\Event $event The beforeFilter event.
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void
	{
		parent::beforeFilter($event);
	}

	/**
	 * Before render callback.
	 * Executes before the view is rendered
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		// Set application name
		$this->set('appName', Configure::readOrFail('App.name'));

		// Stripe Variables
		if ($this->driver == self::DRIVER_STRIPE) {
			// Stripe publishable key
			$this->set('stripePk', Configure::read('Subscriptions.Stripe.publishableKey', ''));
		}

		$this->viewBuilder()
			->setLayout('login');
	}

	/**
	 * Allow client to select a subscription plan as a product.
	 *
	 * @return void
	 */
	public function index(SubscriptionServiceInterface $subscriptionService)
	{
		$clientId = TenantUtility::getTenantIdFromRequest();

		if (empty($clientId)) {
			$this->Flash->error(__('Unable to get client ID associated with the current user.'));

			$this->redirect([
				'_name' => 'login',
			]);

			return;
		}

		/** @var \App\Model\Entity\Client */
		$client = $this->Clients->get($clientId);

		$customerId = $subscriptionService->getClientCustomerId($client);
		$form = new SubscriptionProductForm();

		// for bypassing payment process
		$this->Clients->activateSubscription($client);
		// Load dashboard if client is already subscribed
		if ($client->isSubscribed()) {
			$this->redirect([
				'_name' => 'redirector',
			]);
		}

		// List products from subscription service
		try {
			$products = $subscriptionService->listClientProducts($client);
		} catch (ClientSubscriptionsUnavailableException $e) {
			$products = [];
			Log::write('error', __('No subscription products returned. Details: {0}', $e->getMessage()));
			$this->Flash->error($e->getMessage());
		}

		$this->set(compact('client', 'form', 'products'));

		// Handle form submission
		if ($this->request->is('post')) {
			if ($form->execute($this->request->getData())) {
				// Update client information with new plan
				$client->set([
					'licenses' => $form->getData('licenses'),
					'payment_provider_name' => $this->driver,
					'subscription_product_id' => $form->getData('product_id'),
				]);

				// Create subscription record with service provider
				$subscription = $subscriptionService->createClientSubscription($client, $form->getData());

				// Save subscription ID back to Client entity
				$client->set([
					'payment_provider_subscription_id' => $subscription->id,
				]);

				$this->Clients->saveOrFail($client);

				return $this->redirect([
					'action' => 'payment',
				]);
			} else {
				$this->Flash->error(__(self::SUBSCRIPTION_ERROR));
			}
		}
	}

	/**
	 * Capture payment for subscription
	 *
	 * @return void
	 */
	public function payment(SubscriptionServiceInterface $subscriptionService)
	{
		$clientId = TenantUtility::getTenantIdFromRequest();

		if (empty($clientId)) {
			$this->Flash->error(__('Unable to get client ID associated with the current user.'));

			$this->redirect([
				'_name' => 'login',
			]);

			return;
		}

		/** @var \App\Model\Entity\Client */
		$client = $this->Clients->get($clientId);

		// Load dashboard if client is already subscribed
		if ($client->isSubscribed()) {
			$this->redirect([
				'_name' => 'redirector',
			]);
		}

		if (!$subscriptionService->clientHasSubscription($client)) {
			return $this->redirect([
				'action' => 'index',
			]);
		}

		$subscription = $subscriptionService->getClientSubscription($client);
		$price = $subscriptionService->estimateClientPricing($client);
		$licenses = $client->licenses;

		switch ($this->driver) {
			case self::DRIVER_NULL:
				$form = new NullPaymentForm();
				$template = 'payment_null';
				break;
			case self::DRIVER_STRIPE:
				$form = new StripePaymentForm();
				$this->set('customerId', $subscription->customerId);
				$this->set('clientSecret', $subscription->clientSecret);
				$template = 'payment_stripe';
				break;
			default:
				throw new SubscriptionServiceInvalidDriverException([
					'driver' => $this->driver,
				]);
				break;
		}

		if ($this->request->is('post')) {
			if ($form->execute($this->request->getData())) {
				return $this->redirect([
					'action' => 'postPayment',
				]);
			}
		}

		$this->set(compact(
			'client',
			'subscription',
			'licenses',
			'price',
			'form',
		));

		return $this->render($template);
	}

	/**
	 * Post Payment
	 *
	 * @return void
	 */
	public function postPayment(SubscriptionServiceInterface $subscriptionService)
	{
		$clientId = TenantUtility::getTenantIdFromRequest();

		if (empty($clientId)) {
			$this->Flash->error(__('Unable to get client ID associated with the current user.'));

			$this->redirect([
				'_name' => 'login',
			]);

			return;
		}

		/** @var \App\Model\Entity\Client */
		$client = $this->Clients->get($clientId);

		// Load dashboard if client is already subscribed
		if ($client->isSubscribed()) {
			$this->redirect([
				'_name' => 'redirector',
			]);
		}

		$subscription = $subscriptionService->getClientSubscription($client);

		if (!$subscription->active) {
			$this->Flash->error(__(self::POST_PAYMENT_ERROR));

			return $this->redirect([
				'action' => 'index',
			]);
		}

		/**
		 * @todo Validate posted data is legit
		 */

		// "id" => "pi_****"
		// "object" => "payment_intent"
		// "amount" => "3900"
		// "canceled_at" => ""
		// "cancellation_reason" => ""
		// "capture_method" => "automatic"
		// "client_secret" => "pi_****_secret_****"
		// "confirmation_method" => "automatic"
		// "created" => "1622673922"
		// "currency" => "usd"
		// "description" => "Subscription creation"
		// "last_payment_error" => ""
		// "livemode" => "false"
		// "next_action" => ""
		// "payment_method" => "pm_****"
		// "payment_method_types" => "card"
		// "receipt_email" => ""
		// "setup_future_usage" => "off_session"
		// "shipping" => ""
		// "source" => ""
		// "status" => "succeeded"

		// Enable the client's access
		$this->Clients->activateSubscription($client);

		$this->redirect([
			'_name' => 'redirector',
		]);
	}
}
