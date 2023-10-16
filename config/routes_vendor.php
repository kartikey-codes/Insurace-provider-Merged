<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\InflectedRoute;

/**
 * Vendor Area
 */
$routes->prefix('vendor', function (RouteBuilder $builder) {
	/**
	 * API Routing
	 * Uses API Token Authentication (Stateless)
	 *
	 */
	$builder->prefix('api', function (RouteBuilder $builder) {
		$builder->resources('Appeals', [
			'map' => [
				'complete' => [
					'action' => 'complete',
					'method' => 'POST',
					'path' => '{id}/complete'
				],
				'return' => [
					'action' => 'return',
					'method' => 'POST',
					'path' => '{id}/return'
				],
				'defendable' => [
					'action' => 'defendable',
					'method' => 'PATCH',
					'path' => '{id}/defendable'
				]
			]
		], function (RouteBuilder $subBuilder) {
			$subBuilder->resources('AppealFiles', [
				'actions' => [
					'create' => 'add'
				],
				'only' => [
					'download',
					'create',
					'index',
					'preview',
					'delete',
					'rename'
				],
				'map' => [
					'download' => [
						'action' => 'download',
						'method' => 'GET',
						'path' => 'download/*'
					],
					'preview' => [
						'action' => 'preview',
						'method' => 'GET',
						'path' => 'preview/*'
					],
					'rename' => [
						'action' => 'rename',
						'method' => 'POST',
						'path' => 'rename/*'
					],
					'delete' => [
						'action' => 'delete',
						'method' => 'DELETE',
						'path' => '*'
					]
				],
				'prefix' => 'Appeals',
				'path' => 'files'
			]);

			$subBuilder->resources('AppealNotes', [
				'prefix' => 'Appeals',
				'path' => 'notes'
			]);
		});

		// Cases
		$builder->resources('Cases', [
			'map' => [
				'activity' => [
					'action' => 'activity',
					'method' => 'GET',
					'path' => '{id}/activity'
				],
				'completeAppeals' => [
					'action' => 'completeAppeals',
					'method' => 'GET',
					'path' => '{id}/completeAppeals'
				]
			]
		], function (RouteBuilder $subBuilder) {
			$subBuilder->resources('CaseFiles', [
				'actions' => [
					'create' => 'add'
				],
				'only' => [
					'download',
					'create',
					'index',
					'preview',
					'delete',
					'rename'
				],
				'map' => [
					'download' => [
						'action' => 'download',
						'method' => 'GET',
						'path' => 'download/*'
					],
					'preview' => [
						'action' => 'preview',
						'method' => 'GET',
						'path' => 'preview/*'
					],
					'rename' => [
						'action' => 'rename',
						'method' => 'POST',
						'path' => 'rename/*'
					],
					'delete' => [
						'action' => 'delete',
						'method' => 'DELETE',
						'path' => '*'
					]
				],
				'prefix' => 'Cases',
				'path' => 'files'
			]);
		});

		$builder->resources('IncomingDocuments', [
			'map' => [
				'preview' => [
					'action' => 'preview',
					'method' => 'GET',
					'path' => '{id}/preview'
				]
			]
		]);

		$builder->resources('NotDefendableReasons', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		// Fallbacks
		$builder->connect('/{controller}', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
		$builder->connect('/{controller}/{action}/*', [], ['routeClass' => 'DashedRoute']);

		// Fallback to Dashed routes like /dashed-resource/
		$builder->fallbacks(DashedRoute::class);
	});

	// Single Page Application
	// Handles all routing from here
	$builder->connect('/', [
		'controller' => 'Spa',
		'action' => 'vendor',
		'prefix' => null
	], [
		'_name' => 'vendorSpa'
	]);

	// Allow wildcards so Vue-Router can pick up the URL
	$builder->connect('/*', [
		'controller' => 'Spa',
		'action' => 'vendor',
		'prefix' => null
	]);
});
