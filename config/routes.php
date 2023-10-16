<?php

/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

// Load area routes
require_once 'routes_admin.php';
require_once 'routes_client.php';
require_once 'routes_vendor.php';

/**
 * Application Routing
 * Uses form authentication for rendering html pages
 *
 */
$routes->scope('/', function (RouteBuilder $builder) {

	/**
	 * Director
	 */
	$builder->connect('/', [
		'controller' => 'Auth',
		'action' => 'direct',
		'prefix' => null
	], [
		'_name' => 'redirector'
	]);

	/**
	 * User Registration
	 */
	$builder->connect('/register/user', [
		'controller' => 'UserRegistration',
		'action' => 'index'
	], [
		'_name' => 'userRegister'
	]);

	/**
	 * Redeem Invite Token
	 */
	$builder->connect('/invited', [
		'controller' => 'UserInvite',
		'action' => 'redeem'
	], [
		'_name' => 'inviteTokenRedeem'
	]);

	/**
	 * NPI Lookup
	 */
	$builder->connect('/register/npi-lookup', [
		'controller' => 'NpiLookup',
		'action' => 'index'
	], [
		'_name' => 'registerNpiLookup'
	]);

	/**
	 * Client Self-Registration
	 */
	$builder->connect('/register/client', [
		'controller' => 'ClientRegistration',
		'action' => 'index'
	], [
		'_name' => 'clientRegister'
	]);

	/**
	 * Client Subscription
	 */
	$builder->connect('/subscribe/client', [
		'controller' => 'ClientSubscription',
		'action' => 'index'
	], [
		'_name' => 'clientSubscription'
	]);

	$builder->connect('/subscribe/client/payment', [
		'controller' => 'ClientSubscription',
		'action' => 'payment'
	], [
		'_name' => 'clientSubscriptionPayment'
	]);

	$builder->connect('/subscribe/client/payment/post', [
		'controller' => 'ClientSubscription',
		'action' => 'postPayment'
	], [
		'_name' => 'clientSubscriptionPostPayment'
	]);

	/**
	 * Vendor Self-Registration
	 */
	$builder->connect('/register/vendor', [
		'controller' => 'VendorRegistration',
		'action' => 'index'
	], [
		'_name' => 'vendorRegister'
	]);

	/**
	 * Log In
	 */
	$builder->connect('/login', [
		'controller' => 'Login',
		'action' => 'index',
		'prefix' => null
	], [
		'_name' => 'login'
	]);

	/**
	 * Log Out
	 */
	$builder->connect('/logout', [
		'controller' => 'Logout',
		'action' => 'index'
	], [
		'_name' => 'logout'
	]);

	/**
	 * Forgot Password
	 */
	$builder->connect('/forgot-password', [
		'controller' => 'Auth',
		'action' => 'forgotPassword'
	], [
		'_name' => 'forgotPassword'
	]);

	/**
	 * Reset Password
	 */
	$builder->connect('/reset-password/*', [
		'controller' => 'Auth',
		'action' => 'resetPassword'
	], [
		'_name' => 'resetPassword'
	]);

	/**
	 * Force Change Password
	 */
	$builder->connect('/password-expired', [
		'controller' => 'Auth',
		'action' => 'forceChangePassword'
	], [
		'_name' => 'forceChangePassword'
	]);

	/**
	 * Client Terms & Conditions
	 */
	$builder->connect('/terms/client', [
		'controller' => 'Pages',
		'action' => 'clientTerms'
	], [
		'_name' => 'clientTerms'
	]);

	/**
	 * Vendor Terms & Conditions
	 */
	$builder->connect('/terms/vendor', [
		'controller' => 'Pages',
		'action' => 'vendorTerms'
	], [
		'_name' => 'vendorTerms'
	]);

	/**
	 * Application Health
	 */
	$builder->connect('/app-health', [
		'controller' => 'ApplicationHealth',
		'action' => 'index'
	]);

	$builder->connect('/robots933456.txt', [
		'controller' => 'ApplicationHealth',
		'action' => 'dummyRobots'
	]);

	/**
	 * Connect catchall routes for all controllers.
	 *
	 * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
	 *    `$builder->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
	 *    `$builder->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
	 *
	 * Any route class can be used with this method, such as:
	 * - DashedRoute
	 * - InflectedRoute
	 * - Route
	 * - Or your own route class
	 *
	 * You can remove these routes once you've connected the
	 * routes you want in your application.
	 */

	// Everything under this scope is explicitely defined, so we don't need fallback routes.
	//$builder->fallbacks(InflectedRoute::class);
});
