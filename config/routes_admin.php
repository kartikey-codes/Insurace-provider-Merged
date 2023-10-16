<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\InflectedRoute;

/**
 * Admin Area
 */
$routes->prefix('admin', function (RouteBuilder $builder) {
	/**
	 * API Routing
	 * Uses API Token Authentication (Stateless)
	 *
	 */
	$builder->prefix('api', function (RouteBuilder $builder) {
		$builder->resources('Clients');
		$builder->resources('Vendors');
		$builder->resources('Users');

		$builder->fallbacks(InflectedRoute::class);
	});

	// Single Page Application
	// Handles all routing from here
	$builder->connect('/', [
		'controller' => 'Spa',
		'action' => 'admin',
		'prefix' => null
	], [
		'_name' => 'adminSpa'
	]);

	// Allow wildcards so Vue-Router can pick up the URL
	$builder->connect('/*', [
		'controller' => 'Spa',
		'action' => 'admin',
		'prefix' => null
	]);
});
