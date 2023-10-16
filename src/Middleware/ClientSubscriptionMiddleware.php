<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Application;
use App\Model\Table\ClientsTable;
use App\Service\SubscriptionServiceInterface;
use Cake\Core\ContainerInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ClientSubscriptionMiddleware implements MiddlewareInterface
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Application
	 */
	private Application $_app;

	/**
	 * @var App\Model\Table\ClientsTable
	 */
	private ClientsTable $Clients;

	/**
	 * @var SubscriptionServiceInterface
	 */
	private SubscriptionServiceInterface $subscriptionService;

	/**
	 * Constructor
	 */
	public function __construct(Application $app, SubscriptionServiceInterface $subscriptionService)
	{
		$this->_app = $app;
		$this->subscriptionService = $subscriptionService;
		$this->Clients = $this->fetchTable('Clients');
	}

	/**
	 * Check subscription is active
	 *
	 * @param \Cake\Http\ServerRequest $request
	 * @param \Psr\Http\Server\RequestHandlerInterface $handler
	 * @return \Psr\Http\Message\ResponseInterface
	 * @throws \InvalidArgumentException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// Bypass if subscription service is disabled
		if (!$this->subscriptionService->isEnabled()) {
			return $handler->handle($request);
		}

		// Bypass if subscription service is not required
		if (!$this->subscriptionService->isRequired()) {
			return $handler->handle($request);
		}

		// Skip for requests not detected as to the client area
		if (!$request->is('client')) {
			return $handler->handle($request);
		}

		// Redirect to login if we're not authenticated
		$identity = $request->getAttribute('identity');
		if (empty($identity)) {
			return new RedirectResponse('/login');
		}

		/** @var \App\Model\Entity\User */
		$user = $identity->getOriginalData();
		if (empty($user)) {
			return new RedirectResponse('/login');
		}

		// Skip for admins
		if ($user->isAdmin()) {
			return $handler->handle($request);
		}

		// Redirect user with empty client ID to login screen (to register)
		if (empty($user->client_id)) {
			return new RedirectResponse('/login');
		}

		// Ensure user is a client user
		if (!$user->isClientUser()) {
			throw new \RuntimeException(__('User is not a client user. Unable to look up client.'));
		}

		try {
			/** @var \App\Middleware\App\Model\Entity\Client */
			$client = $this->Clients->get($user->client_id, ['skipTenantCheck' => true]);
		} catch (RecordNotFoundException) {
			// Client not found, might have been deleted or db wipe during development
			return new RedirectResponse('/login');
		}

		// Continue for subscribed client
		if ($client->isSubscribed()) {
			return $handler->handle($request);
		}

		// We're a client without a subscription, so redirect to payment page.
		return new RedirectResponse('/subscribe/client');
	}
}
