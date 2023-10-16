<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use App\Command\CheckStorageCommand;
use App\Command\NpiNumberLookupCommand;
use App\Command\NpiOrganizationNameLookupCommand;
use App\Command\PostDeploymentCommand;
use App\Event\ModelInitializationListener;
use App\Exception\StorageServiceInvalidDriverException;
use App\Exception\SubscriptionServiceInvalidDriverException;
use App\Middleware\ClientSubscriptionMiddleware;
use App\Middleware\ContainerInjectorMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\DatabaseRetryMiddleware;
use App\Policy\RequestPolicy;
use App\Service\DocumentService;
use App\Service\DocumentServiceInterface;
use App\Service\LicenseService;
use App\Service\LicenseServiceInterface;
use App\Service\NpiRegistryService;
use App\Service\NpiServiceInterface;
use App\Service\StorageServiceInterface;
use App\Service\Storage\AzureBlobStorageService;
use App\Service\Storage\LocalFileStorageService;
use App\Service\SubscriptionServiceInterface;
use App\Service\Subscription\StripeSubscriptionService;
use App\Service\Subscription\NullSubscriptionService;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Middleware\RequestAuthorizationMiddleware;
use Authorization\Policy\MapResolver;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\EventManager;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface, AuthorizationServiceProviderInterface
{
	// Storage Drivers
	private const STORAGE_DRIVER_AZURE = 'azure';
	private const STORAGE_DRIVER_LOCAL = 'local';

	// Subscription Drivers
	private const SUBSCRIPTION_DRIVER_NULL = 'null';
	private const SUBSCRIPTION_DRIVER_STRIPE = 'stripe';

	/**
	 * @inheritDoc
	 */
	public function bootstrap(): void
	{
		// Call parent to load bootstrap from files.
		parent::bootstrap();

		if (PHP_SAPI === 'cli') {
			$this->bootstrapCli();
		}

		/**
		 * Only try to load DebugKit in development mode
		 * Debug Kit should not be installed on a production system
		 */
		$loadDebugKit = env('CAKEPHP_DEBUG_KIT_ENABLED', false);
		if (Configure::read('debug') && $loadDebugKit) {
			$this->addPlugin('DebugKit');
		}

		// Authentication
		$this->addPlugin('Authentication');

		// Authorization
		$this->addPlugin('Authorization');

		// CSV View
		$this->addPlugin('CsvView');

		// Database Log Engine Plugin
		$this->addPlugin('DatabaseLog', [
			/** @see https://github.com/dereuromark/CakePHP-DatabaseLog/issues/42 */
			'routes' => false,
		]);

		// Model Search Plugin
		$this->addPlugin('Search');

		// Soft-Delete Plugin
		$this->addPlugin('Muffin/Trash');

		// PDF Generation Plugin
		$this->addPlugin('CakePdf', [
			'bootstrap' => true,
		]);

		// Model Audit History Plugin
		$this->addPlugin('AuditStash');

		// Load IDE Helper in Debug mode for generating docblock annotations
		if (Configure::read('debug')) {
			$this->addPlugin('IdeHelper');
		}

		// Load listeners
		EventManager::instance()->on(new ModelInitializationListener());
	}

	/**
	 * Dependency injection services
	 *
	 * @param \Cake\Core\ContainerInterface $container DI container
	 * @return void
	 */
	public function services(ContainerInterface $container): void
	{
		// Configure Document Service
		$container->add(DocumentServiceInterface::class, DocumentService::class);

		// Configure NPI Service
		$container->add(NpiServiceInterface::class, NpiRegistryService::class);

		// Configure License Service
		$container->add(LicenseServiceInterface::class, LicenseService::class);

		// Configure Storage Service
		$storageDriver = Configure::readOrFail('Storage.driver');
		$this->loadStorageService($container, $storageDriver);

		// Configure Subscription Service
		$subscriptionDriver = Configure::readOrFail('Subscriptions.driver');
		$this->loadSubscriptionService($container, $subscriptionDriver);

		// Configure Commands
		$container
			->add(NpiNumberLookupCommand::class)
			->addArgument(NpiServiceInterface::class);

		$container
			->add(NpiOrganizationNameLookupCommand::class)
			->addArgument(NpiServiceInterface::class);

		$container
			->add(PostDeploymentCommand::class)
			->addArgument(StorageServiceInterface::class);

		$container
			->add(CheckStorageCommand::class)
			->addArgument(StorageServiceInterface::class);
	}

	/**
	 * Load storage service based on application configuration
	 *
	 * @param \Cake\Core\ContainerInterface $container
	 * @param string $driver
	 * @return void
	 */
	private function loadStorageService(ContainerInterface $container, string $driver): void
	{
		switch ($driver) {
			case self::STORAGE_DRIVER_AZURE:
				$container->add(StorageServiceInterface::class, AzureBlobStorageService::class);
				break;
			case self::STORAGE_DRIVER_LOCAL:
				$container->add(StorageServiceInterface::class, LocalFileStorageService::class);
				break;
			default:
				// Undefined / Unsupported
				throw new StorageServiceInvalidDriverException([
					'driver' => $driver,
				]);
		}
	}

	/**
	 * Load service based on application configuration
	 *
	 * @param \Cake\Core\ContainerInterface $container
	 * @param string $driver
	 * @return void
	 */
	private function loadSubscriptionService(ContainerInterface $container, string $driver): void
	{
		switch (strtolower($driver)) {
			case self::SUBSCRIPTION_DRIVER_NULL:
				$container->add(SubscriptionServiceInterface::class, NullSubscriptionService::class);
				break;
			case self::SUBSCRIPTION_DRIVER_STRIPE:
				$container->add(SubscriptionServiceInterface::class, StripeSubscriptionService::class);
				break;
			default:
				// Undefined / Unsupported
				throw new SubscriptionServiceInvalidDriverException([
					'driver' => $driver,
				]);
		}
	}

	/**
	 * Setup the middleware queue your application will use.
	 *
	 * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
	 * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
	 */
	public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
	{
		$middlewareQueue
			// Catch any exceptions in the lower layers,
			// and make an error page/response
			->add(new ErrorHandlerMiddleware(Configure::read('Error')))

			// Handle plugin/theme assets like CakePHP normally does.
			->add(new AssetMiddleware([
				'cacheTime' => Configure::read('Asset.cacheTime'),
			]))

			// Parse request data into JSON/XML
			->add(new BodyParserMiddleware())

			// Add routing middleware.
			// If you have a large number of routes connected, turning on routes
			// caching in production could improve performance. For that when
			// creating the middleware instance specify the cache config name by
			// using it's second constructor argument:
			// `new RoutingMiddleware($this, '_cake_routes_')`
			->add(new RoutingMiddleware($this))

			// Authentication
			->add(new AuthenticationMiddleware($this))

			// Authorization
			// Required to be after authentication
			->add(new AuthorizationMiddleware($this))
			->add(new RequestAuthorizationMiddleware())

			// Custom Application Middleware
			// @custom
			->add(new ContainerInjectorMiddleware($this->getContainer()))
			->add(new ClientSubscriptionMiddleware($this, $this->getContainer()->get(SubscriptionServiceInterface::class)))
			->add(new DatabaseRetryMiddleware($this))
			->add(new CorsMiddleware());

		return $middlewareQueue;
	}

	/**
	 * @return void
	 */
	protected function bootstrapCli(): void
	{
		try {
			$this->addPlugin('Bake');
		} catch (MissingPluginException $e) {
			// Do not halt if the plugin is missing
		}

		$this->addPlugin('Migrations');

		// Load more plugins here
	}

	/**
	 * Returns a service provider instance.
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request
	 * @return \Authentication\AuthenticationServiceInterface
	 */
	public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
	{
		$service = new AuthenticationService();

		$prefix = $request->getAttribute('prefix');

		// Do not redirect for Api requests
		if ($prefix == 'Api') {
			$config = [
				'unauthenticatedRedirect' => null,
				'queryParam' => 'redirect',
			];
		} else {
			$config = [
				'unauthenticatedRedirect' => '/login',
				'queryParam' => 'redirect',
			];
		}

		$fields = [
			IdentifierInterface::CREDENTIAL_USERNAME => 'email',
			IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
		];

		// Define where users should be redirected to when they are not authenticated
		$service->setConfig($config);

		// Load the authenticators. Session should be first.
		$service->loadAuthenticator('Authentication.Session');

		$service->loadAuthenticator('Authentication.Form', [
			'fields' => $fields,
			'loginUrl' => '/login',
		]);

		$service->loadAuthenticator('Authentication.Token', [
			'queryParam' => 'token',
			'header' => 'Authorization',
			'tokenPrefix' => 'Bearer',
		]);

		// Load identifiers
		$service->loadIdentifier('Authentication.Password', compact('fields'));

		$service->loadIdentifier('Authentication.Token', [
			'tokenField' => 'api_token', // Field on the resolver (user entity)
			'dataField' => 'token', // Query string param name
			'resolver' => [
				'className' => 'Authentication.Orm',
				'userModel' => 'Users',
				'finder' => 'auth',
			],
		]);

		return $service;
	}

	/**
	 * Returns a service provider instance.
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request
	 * @return \Authorization\AuthorizationServiceInterface
	 */
	public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
	{
		$resolver = new MapResolver();

		// Map resolver only supports 1 policy per resource
		// So App\Policy\RequestPolicy implements additional policies
		$resolver->map(ServerRequest::class, RequestPolicy::class);

		return new AuthorizationService($resolver);
	}
}
