<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\InflectedRoute;

/**
 * Client Area
 */
$routes->prefix('client', function (RouteBuilder $builder) {
	/**
	 * API Routing
	 * Uses API Token Authentication (Stateless)
	 *
	 */
	$builder->prefix('api', function (RouteBuilder $builder) {
		$builder->resources('Agencies', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('Clients', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('ClientTypes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('Appeals', [
			'map' => [
				'assign' => [
					'action' => 'assign',
					'method' => 'POST',
					'path' => '{id}/assign'
				],
				'submit' => [
					'action' => 'submit',
					'method' => 'POST',
					'path' => '{id}/submit'
				],
				'complete' => [
					'action' => 'complete',
					'method' => 'POST',
					'path' => '{id}/complete'
				],
				'utc' => [
					'action' => 'utc',
					'method' => 'POST',
					'path' => '{id}/utc'
				],
				'defendable' => [
					'action' => 'defendable',
					'method' => 'POST',
					'path' => '{id}/defendable'
				],
				'reopen' => [
					'action' => 'reopen',
					'method' => 'POST',
					'path' => '{id}/reopen'
				],
				'cancel' => [
					'action' => 'cancel',
					'method' => 'POST',
					'path' => '{id}/cancel'
				],
				'close' => [
					'action' => 'close',
					'method' => 'POST',
					'path' => '{id}/close'
				],
				'pdf' => [
					'action' => 'pdf',
					'method' => 'GET',
					'path' => '{id}/pdf'
				],
				'coverPage' => [
					'action' => 'coverPage',
					'method' => 'GET',
					'path' => '{id}/coverPage'
				],
				'generateCoverPage' => [
					'action' => 'generateCoverPage',
					'method' => 'POST',
					'path' => '{id}/generateCoverPage'
				],
				'openByFacility' => [
					'action' => 'openByFacility',
					'method' => 'GET',
					'path' => 'openByFacility'
				],
				'openByAssignedUser' => [
					'action' => 'openByAssignedUser',
					'method' => 'GET',
					'path' => 'openByAssignedUser'
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
					'rename',
					'merge',
					'zip'
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
						'path' => 'preview/*',
						'pass' => [
							'fileName'
						]
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
					],
					'merge' => [
						'action' => 'merge',
						'method' => 'POST',
						'path' => 'merge/*'
					],
					'zip' => [
						'action' => 'zip',
						'method' => 'POST',
						'path' => 'zip/*'
					]
				],
				'prefix' => 'Appeals',
				'path' => 'files'
			]);

			$subBuilder->resources('AppealNotes', [
				'prefix' => 'Appeals',
				'path' => 'notes'
			]);

			$subBuilder->resources('AppealPackets', [
				'prefix' => 'Appeals',
				'path' => 'packet',
				'map' => [
					'download' => [
						'action' => 'download',
						'method' => 'GET',
						'path' => 'download'
					],
					'exists' => [
						'action' => 'exists',
						'method' => 'GET',
						'path' => 'exists'
					],
					'generate' => [
						'action' => 'generate',
						'method' => 'POST',
						'path' => 'generate'
					],
					'submit' => [
						'action' => 'submit',
						'method' => 'POST',
						'path' => 'submit'
					],
				]
			]);
		});

		$builder->resources('AppealLevels', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('AppealTypes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('AuditReviewers', [
			'map' => [
				'active' => [
					'action' => 'active',
					'method' => 'GET',
					'path' => 'active'
				],
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('CaseOutcomes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('CaseTypes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		// Cases
		$builder->resources('Cases', [
			'map' => [
				'assign' => [
					'action' => 'assign',
					'method' => 'POST',
					'path' => '{id}/assign'
				],
				'close' => [
					'action' => 'close',
					'method' => 'POST',
					'path' => '{id}/close'
				],
				'open' => [
					'action' => 'open',
					'method' => 'POST',
					'path' => '{id}/open'
				],
				'activity' => [
					'action' => 'activity',
					'method' => 'GET',
					'path' => '{id}/activity'
				],
				'empty' => [
					'action' => 'empty',
					'method' => 'GET',
					'path' => '/empty'
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
					'rename',
					'merge',
					'zip'
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
						'path' => 'preview/*',
						'pass' => [
							'fileName'
						]
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
					],
					'merge' => [
						'action' => 'merge',
						'method' => 'POST',
						'path' => 'merge/*'
					],
					'zip' => [
						'action' => 'zip',
						'method' => 'POST',
						'path' => 'zip/*'
					]
				],
				'prefix' => 'Cases',
				'path' => 'files'
			]);

			$subBuilder->resources('CaseRequests', [
				'prefix' => 'Cases',
				'path' => 'requests',
				'map' => [
					'assign' => [
						'action' => 'assign',
						'method' => 'POST',
						'path' => '{id}/assign'
					],
					'close' => [
						'action' => 'close',
						'method' => 'POST',
						'path' => '{id}/close'
					],
					'reopen' => [
						'action' => 'reopen',
						'method' => 'POST',
						'path' => '{id}/reopen'
					],
					'utc' => [
						'action' => 'utc',
						'method' => 'POST',
						'path' => '{id}/utc'
					],
				]
			]);
		});

		$builder->resources('ClientEmployees', [
			'map' => [
				'lookup' => [
					'action' => 'lookup',
					'method' => 'POST'
				]
			]
		]);

		$builder->resources('DaysToRespondFroms', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('DenialTypes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('DenialReasons', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('Disciplines', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('Facilities', [
			'map' => [
				'active' => [
					'action' => 'active',
					'method' => 'GET',
					'path' => 'active'
				],
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
				'npiLookup' => [
					'action' => 'npiLookup',
					'method' => 'POST',
					'path' => 'npiLookup'
				]
			]
		]);

		$builder->resources('FacilityTypes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('GuestPortals', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('IncomingDocuments', [
			'map' => [
				'count' => [
					'action' => 'count',
					'method' => 'GET',
					'path' => 'count'
				],
				'assign' => [
					'action' => 'assign',
					'method' => 'POST',
					'path' => '{id}/assign'
				],
				'edit' => [
					'action' => 'edit',
					'method' => 'POST',
					'path' => '{id}/edit'
				],
				'download' => [
					'action' => 'download',
					'method' => 'GET',
					'path' => '{id}/download'
				],
				'preview' => [
					'action' => 'preview',
					'method' => 'GET',
					'path' => '{id}/preview'
				],
				'attach_case' => [
					'action' => 'attachCase',
					'method' => 'POST',
					'path' => '{id}/attach_case'
				],
				'detach_case' => [
					'action' => 'detachCase',
					'method' => 'POST',
					'path' => '{id}/detach_case'
				],
				'attach_appeal' => [
					'action' => 'attachAppeal',
					'method' => 'POST',
					'path' => '{id}/attach_appeal'
				],
				'detach_appeal' => [
					'action' => 'detachAppeal',
					'method' => 'POST',
					'path' => '{id}/detach_appeal'
				],
				'bulk_assign' => [
					'action' => 'bulkAssign',
					'method' => 'POST',
					'path' => '{id}/bulk_assign'
				]
			]
		]);

		$builder->resources('InsuranceProviders', [
			'map' => [
				'active' => [
					'action' => 'active',
					'method' => 'GET',
					'path' => 'active'
				],
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				]
			]
		], function (RouteBuilder $subBuilder) {
			$subBuilder->resources('InsuranceTypes', [
				'only' => [
					'index'
				],
				'prefix' => 'InsuranceProviders',
				'path' => 'insurance_types'
			]);

			$subBuilder->resources('AppealLevels', [
				'only' => [
					'index'
				],
				'prefix' => 'InsuranceProviders',
				'path' => 'appeal_levels'
			]);
		});

		$builder->resources('InsuranceTypes', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('LibraryFiles', [
			'actions' => [
				'create' => 'add'
			],
			'only' => [
				'download',
				'create',
				'index',
				'preview',
				'delete',
				'rename',
				'merge',
				'zip'
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
					'path' => 'preview/*',
					'pass' => [
						'fileName'
					]
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
				],
				'merge' => [
					'action' => 'merge',
					'method' => 'POST',
					'path' => 'merge/*'
				],
				'zip' => [
					'action' => 'zip',
					'method' => 'POST',
					'path' => 'zip/*'
				]
			],
			'path' => 'library'
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

		$builder->resources('OutgoingDocuments', [
			'map' => [
				'count' => [
					'action' => 'count',
					'method' => 'GET',
					'path' => 'count'
				],
				'index' => [
					'action' => 'index',
					'method' => 'GET',
					'path' => 'index'
				],
				'download' => [
					'action' => 'download',
					'method' => 'GET',
					'path' => '{id}/download'
				],
				'preview' => [
					'action' => 'preview',
					'method' => 'GET',
					'path' => '{id}/preview'
				],
				'cancel' => [
					'action' => 'cancel',
					'method' => 'POST',
					'path' => '{id}/cancel'
				],
				'delivered' => [
					'action' => 'delivered',
					'method' => 'POST',
					'path' => '{id}/delivered'
				],
				'retry' => [
					'action' => 'retry',
					'method' => 'POST',
					'path' => '{id}/retry'
				],
			]
		]);

		$builder->resources('Patients', [
			'map' => [
				'similar' => [
					'action' => 'similar',
					'method' => 'GET',
					'path' => '{id}/similar'
				],
				'merge' => [
					'action' => 'merge',
					'method' => 'POST',
					'path' => '{id}/merge'
				]
			]
		]);

		$builder->resources('ReferenceNumbers', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('UtcReasons', [
			'actions' => [
				'create' => 'add'
			],
			'only' => [
				'create',
				'edit',
				'all',
				'view',
				'index',
				'delete'
			],
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			]
		]);

		$builder->resources('Users', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
				'new' => [
					'action' => 'new',
					'method' => 'GET',
					'path' => 'new'
				],
				'enable' => [
					'action' => 'enable',
					'method' => 'POST',
					'path' => '{id}/enable'
				],
				'disable' => [
					'action' => 'disable',
					'method' => 'POST',
					'path' => '{id}/disable'
				],
				'unlock' => [
					'action' => 'unlock',
					'method' => 'POST',
					'path' => '{id}/unlock'
				],
				'setPassword' => [
					'action' => 'setPassword',
					'method' => 'POST',
					'path' => '{id}/set_password'
				]
			]
		]);

		$builder->resources('UserInvites', [
			'only' => [
				'create'
			]
		]);

		$builder->resources('Permissions', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				]
			]
		]);

		$builder->resources('Roles', [
			'actions' => [
				'create' => 'add'
			],
			'only' => [
				'create',
				'edit',
				'all',
				'view',
				'index',
				'delete',
				'addUser',
				'removeUser'
			],
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
				'addUser' => [
					'action' => 'addUser',
					'method' => 'POST',
					'path' => '{id}/add-user'
				],
				'removeUser' => [
					'action' => 'removeUser',
					'method' => 'POST',
					'path' => '{id}/remove-user'
				]
			]
		]);

		// $builder->resources('Vendors', [
		// 	'map' => [
		// 		'all' => [
		// 			'action' => 'all',
		// 			'method' => 'GET',
		// 			'path' => 'all'
		// 		],
		// 	],
		// 	'inflect' => 'underscore'
		// ]);

		$builder->resources('Services', [
			'map' => [
				'active' => [
					'action' => 'active',
					'method' => 'GET',
					'path' => 'active'
				],
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				]
			]
		]);

		$builder->resources('Specialties', [
			'map' => [
				'all' => [
					'action' => 'all',
					'method' => 'GET',
					'path' => 'all'
				],
			],
			'inflect' => 'underscore'
		]);

		// Calendar
		$builder->connect('/calendar', [
			'controller' => 'Calendar',
			'action' => 'index'
		]);

		$builder->connect('/calendar/*', [
			'controller' => 'Calendar',
			'action' => 'view'
		]);

		// Client Settings
		$builder->connect('/settings', [
			'controller' => 'ClientSettings',
			'action' => 'view'
		]);

		// Integrations
		$builder->connect('/integrations', [
			'controller' => 'Integrations',
			'action' => 'index'
		]);

		$builder->connect('/integrations/update', [
			'controller' => 'Integrations',
			'action' => 'update'
		]);

		// Auth - Update Profile
		$builder->connect('/auth/update-profile', [
			'controller' => 'Auth',
			'action' => 'updateProfile'
		], [
			'routeClass' => 'DashedRoute'
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
		'action' => 'client',
		'prefix' => null
	], [
		'_name' => 'clientSpa'
	]);

	// Allow wildcards so Vue-Router can pick up the URL
	$builder->connect('/*', [
		'controller' => 'Spa',
		'action' => 'client',
		'prefix' => null
	]);
	$builder->connect('/ftp', [
		'controller' => 'Ftp',
		'action' => 'index',
	    '_method' => 'POST'
	]);
	$builder->connect('/incoming', [
		'controller' => 'Incoming',
		'action' => 'index',
	]);

	// for reading documents send by textract flask app
	$builder->connect('/textract', [
		'controller' => 'Textract',
		'action' => 'index',
		
	]);

	$builder->connect('/request', [
		'controller' => 'Request',
		'action' => 'index',
		
	]);

	$builder->connect('/insuranceappeal', [
		'controller' => 'InsuranceAppeal',
		'action' => 'index',
		
	]);
	$builder->connect('/addtype', [
		'controller' => 'Type',
		'action' => 'index',
		'_method' => 'POST'
	]);

});
