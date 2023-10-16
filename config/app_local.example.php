<?php

declare(strict_types=1);

use Cake\Console\ConsoleOutput;
use Cake\Log\Engine\ConsoleLog;
use Cake\Log\Engine\SyslogLog;

/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 *
 * Docker specific configuration overrides are in /docker/app/app_local.docker.php.
 * This file is replaced by the docker configuration when building the image.
 *
 * These configuration overrides are intended for local development or any
 * non-container based environment.
 */

return [
	/*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
	'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

	/*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
	'Security' => [
		'salt' => env('SECURITY_SALT', '__SALT__'),
	],

	/*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
	// 'Datasources' => [
	//     'default' => [
	//         'host' => 'localhost',
	//         //'port' => 'non_standard_port_number',
	//         'username' => 'my_app',
	//         'password' => 'secret',
	//         'database' => 'my_app',
	//         /**
	//          * If not using the default 'public' schema with the PostgreSQL driver
	//          * set it here.
	//          */
	//         //'schema' => 'myapp',

	//         /**
	//          * You can use a DSN string to set the entire configuration
	//          */
	//         'url' => env('DATABASE_URL', null),
	//     ],

	//     /*
	//      * The test connection is used during the test suite.
	//      */
	//     'test' => [
	//         'host' => 'localhost',
	//         //'port' => 'non_standard_port_number',
	//         'username' => 'my_app',
	//         'password' => 'secret',
	//         'database' => 'test_myapp',
	//         //'schema' => 'myapp',
	//         'url' => env('DATABASE_TEST_URL', null),
	//     ],
	// ],

	/*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
	// 'EmailTransport' => [
	//     'default' => [
	//         'host' => 'localhost',
	//         'port' => 25,
	//         'username' => null,
	//         'password' => null,
	//         'client' => null,
	//         'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
	//     ],
	// ],

	/*
 	 * Configures logging options
 	 */
	// 'Log' => [
	// 	'debug' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	'error' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	/**
	// 	 * App Logs
	// 	 */
	// 	// User Logins
	// 	'login' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// General
	// 	'general' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// File Storage
	// 	'storage' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// Emails
	// 	'emails' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// Object Relational Mapper Log
	// 	'orm' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// Tuning / Debug Logs
	// 	'tuning' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// Deployment Logs
	// 	'deployment' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// 	// To enable this dedicated query log, you need set your datasource's log flag to true
	// 	'queries' => [
	// 		'className' => SyslogLog::class,
	// 		'engine' => 'Syslog'
	// 	],
	// ],
];
