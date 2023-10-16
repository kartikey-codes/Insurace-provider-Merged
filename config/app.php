<?php

declare(strict_types=1);

use Cake\Cache\Engine\FileEngine;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlserver;
use Cake\Error\Renderer\WebExceptionRenderer;
use Cake\Log\Engine\ConsoleLog;
use Cake\Log\Engine\FileLog;
use Cake\Mailer\Transport\MailTransport;
use Cake\Mailer\Transport\SmtpTransport;

// CakePHP Configuration
//
// Keep all configuration values used throughout the app in this file.
// Reference any keys or sensitive values with env() for .env files.
// Don't store any keys in here! This file gets committed to version control,
// which is different from standard CakePHP.
//
// @note VSCode keeps tabbing this block out whenever it's a multi-line comment.

return [

	/**
	 * Debug Level:
	 *
	 * Production Mode:
	 * false: No error messages, errors, or warnings shown.
	 *
	 * Development Mode:
	 * true: Errors and warnings shown.
	 */
	'debug' => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),

	/**
	 * Configure basic information about the application.
	 *
	 * - namespace - The namespace to find app classes under.
	 * - defaultLocale - The default locale for translation, formatting currencies and numbers, date and time.
	 * - encoding - The encoding used for HTML + database connections.
	 * - base - The base directory the app resides in. If false this
	 *   will be auto detected.
	 * - dir - Name of app directory.
	 * - webroot - The webroot directory.
	 * - wwwRoot - The file path to webroot.
	 * - baseUrl - To configure CakePHP to *not* use mod_rewrite and to
	 *   use CakePHP pretty URLs, remove these .htaccess
	 *   files:
	 *      /.htaccess
	 *      /webroot/.htaccess
	 *   And uncomment the baseUrl key below.
	 * - fullBaseUrl - A base URL to use for absolute links. When set to false (default)
	 *   CakePHP generates required value based on `HTTP_HOST` environment variable.
	 *   However, you can define it manually to optimize performance or if you
	 *   are concerned about people manipulating the `Host` header.
	 * - imageBaseUrl - Web path to the public images directory under webroot.
	 * - cssBaseUrl - Web path to the public css directory under webroot.
	 * - jsBaseUrl - Web path to the public js directory under webroot.
	 * - paths - Configure paths for non class based resources. Supports the
	 *   `plugins`, `templates`, `locales` subkeys, which allow the definition of
	 *   paths for plugins, view templates and locale files respectively.
	 */
	'App' => [
		'name' => env('APP_NAME', 'RevKeep'), // @custom
		'namespace' => 'App',
		'encoding' => env('APP_ENCODING', 'UTF-8'),
		'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
		'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'America/Detroit'),
		'base' => false,
		'dir' => 'src',
		'webroot' => 'webroot',
		'wwwRoot' => WWW_ROOT,
		//'baseUrl' => env('SCRIPT_NAME'),
		'fullBaseUrl' => env('FULL_BASE_URL', null),
		'imageBaseUrl' => 'img/',
		'cssBaseUrl' => 'css/',
		'jsBaseUrl' => 'js/',
		'paths' => [
			'plugins' => [ROOT . DS . 'plugins' . DS],
			'templates' => [ROOT . DS . 'templates' . DS],
			'locales' => [ROOT . DS . 'locales' . DS],
		],
	],

	/**
	 * Security and encryption configuration
	 *
	 * - salt - A random string used in security hashing methods.
	 *   The salt value is also used as the encryption key.
	 *   You should treat it as extremely sensitive data.
	 */
	'Security' => [
		'salt' => env('SECURITY_SALT', "__SALT__"),
	],

	/**
	 * Apply timestamps with the last modified time to static assets (js, css, images).
	 * Will append a querystring parameter containing the time the file was modified.
	 * This is useful for busting browser caches.
	 *
	 * Set to true to apply timestamps when debug is true. Set to 'force' to always
	 * enable timestamping regardless of debug value.
	 *
	 * @note The laravel-mix helper and manifest is used instead of the
	 * CakePHP asset helper to cache front end bundle hashes.
	 */
	'Asset' => [
		//'timestamp' => true,
		// 'cacheTime' => '+1 year'
	],

	/**
	 * Configure the cache adapters.
	 *
	 * @note File based caches can be used in containers since cache should be
	 * cleared when restarting a container anyways.
	 */
	'Cache' => [
		'default' => [
			'className' => FileEngine::class,
			'path' => CACHE,
			'url' => env('CACHE_DEFAULT_URL', null),
		],
		/**
		 * Time-Based Cache
		 */
		'veryshort' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+2 minutes',
			'path' => CACHE,
			'prefix' => env('APP_KEY', 'app') . '_veryshort_',
			'url' => env('CACHE_VERYSHORT_URL', null),
		],
		'short' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+10 minutes',
			'path' => CACHE,
			'prefix' => env('APP_KEY', 'app') . '_short_',
			'url' => env('CACHE_SHORT_URL', null),
		],
		'medium' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+30 minutes',
			'path' => CACHE,
			'prefix' => env('APP_KEY', 'app') . '_medium_',
			'url' => env('CACHE_MEDIUM_URL', null),
		],
		'long' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+8 hours',
			'path' => CACHE,
			'prefix' => env('APP_KEY', 'app') . '_long_',
			'url' => env('CACHE_LONG_URL', null),
		],
		'verylong' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+2 weeks',
			'path' => CACHE,
			'prefix' => env('APP_KEY', 'app') . '_verylong_',
			'url' => env('CACHE_VERYLONG_URL', null),
		],
		/**
		 * Result Type Caches
		 */
		'all' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+2 months',
			'path' => CACHE . DS . 'all',
			'prefix' => 'all_',
			'url' => env('CACHE_ALL_URL', null),
		],
		'active' => [
			'className' => 'TenantAware',
			'engine' => FileEngine::class,
			'duration' => '+2 months',
			'path' => CACHE . DS . 'active',
			'prefix' => 'active_',
			'url' => env('CACHE_ACTIVE_URL', null),
		],
		'npi' => [
			// Not tenant aware
			'className' => FileEngine::class,
			'duration' => '+2 months',
			'path' => CACHE . DS . 'npi',
			'prefix' => 'npi_',
			'url' => env('CACHE_NPI_URL', null),
		],
		/**
		 * Specific Caches
		 */
		'permissions' => [
			'className' => FileEngine::class,
			'duration' => '+8 hours',
			'path' => CACHE,
			'prefix' => env('APP_KEY', 'app') . '_permissions_',
			'url' => env('CACHE_PERMISSIONS_URL', null),
		],

		/**
		 * Configure the cache used for general framework caching.
		 * Translation cache files are stored with this configuration.
		 * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
		 * If you set 'className' => 'Null' core cache will be disabled.
		 */
		'_cake_core_' => [
			'className' => FileEngine::class,
			'prefix' => env('APP_KEY', 'app') . '_cake_core_',
			'path' => CACHE . 'persistent/',
			'serialize' => true,
			'duration' => '+1 years',
			'url' => env('CACHE_CAKECORE_URL', null),
		],

		/**
		 * Configure the cache for model and datasource caches. This cache
		 * configuration is used to store schema descriptions, and table listings
		 * in connections.
		 * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
		 */
		'_cake_model_' => [
			'className' => FileEngine::class,
			'prefix' => env('APP_KEY', 'app') . '_cake_model_',
			'path' => CACHE . 'models/',
			'serialize' => true,
			'duration' => '+1 years',
			'url' => env('CACHE_CAKEMODEL_URL', null),
		],

		/**
		 * Configure the cache for routes. The cached routes collection is built the
		 * first time the routes are processed via `config/routes.php`.
		 * Duration will be set to '+2 seconds' in bootstrap.php when debug = true
		 */
		'_cake_routes_' => [
			'className' => FileEngine::class,
			'prefix' => env('APP_KEY', 'app') . '_cake_routes_',
			'path' => CACHE,
			'serialize' => true,
			'duration' => '+1 years',
			'url' => env('CACHE_CAKEROUTES_URL', null),
		],
	],

	/**
	 * Configure the Error and Exception handlers used by your application.
	 *
	 * By default errors are displayed using Debugger, when debug is true and logged
	 * by Cake\Log\Log when debug is false.
	 *
	 * In CLI environments exceptions will be printed to stderr with a backtrace.
	 * In web environments an HTML page will be displayed for the exception.
	 * With debug true, framework errors like Missing Controller will be displayed.
	 * When debug is false, framework errors will be coerced into generic HTTP errors.
	 *
	 * Options:
	 *
	 * - `errorLevel` - int - The level of errors you are interested in capturing.
	 * - `trace` - boolean - Whether or not backtraces should be included in
	 *   logged errors/exceptions.
	 * - `log` - boolean - Whether or not you want exceptions logged.
	 * - `exceptionRenderer` - string - The class responsible for rendering
	 *   uncaught exceptions. If you choose a custom class you should place
	 *   the file for that class in src/Error. This class needs to implement a
	 *   render method.
	 * - `skipLog` - array - List of exceptions to skip for logging. Exceptions that
	 *   extend one of the listed exceptions will also be skipped for logging.
	 *   E.g.:
	 *   `'skipLog' => ['Cake\Network\Exception\NotFoundException', 'Cake\Network\Exception\UnauthorizedException']`
	 * - `extraFatalErrorMemory` - int - The number of megabytes to increase
	 *   the memory limit by when a fatal error is encountered. This allows
	 *   breathing room to complete logging or error handling.
	 */
	'Error' => [
		'errorLevel' => E_ALL,
		//'exceptionRenderer' => WebExceptionRenderer::class,
		'skipLog' => [],
		'log' => true,
		'trace' => true,
		'ignoredDeprecationPaths' => [
			// @see https://github.com/lorenzo/audit-stash/pull/61
			'vendor\lorenzo\audit-stash\src\Model\Behavior\AuditLogBehavior.php',
		]
	],

	/**
	 * Debugger configuration
	 *
	 * Define development error values for Cake\Error\Debugger
	 *
	 * - `editor` Set the editor URL format you want to use.
	 *   By default atom, emacs, macvim, phpstorm, sublime, textmate, and vscode are
	 *   available. You can add additional editor link formats using
	 *   `Debugger::addEditor()` during your application bootstrap.
	 * - `outputMask` A mapping of `key` to `replacement` values that
	 *   `Debugger` should replace in dumped data and logs generated by `Debugger`.
	 */
	'Debugger' => [
		'editor' => 'vscode',
	],

	/**
	 * Email configuration.
	 *
	 * By defining transports separately from delivery profiles you can easily
	 * re-use transport configuration across multiple profiles.
	 *
	 * You can specify multiple configurations for production, development and
	 * testing.
	 *
	 * Each transport needs a `className`. Valid options are as follows:
	 *
	 *  Mail   - Send using PHP mail function
	 *  Smtp   - Send using SMTP
	 *  Debug  - Do not send the email, just return the result
	 *
	 * You can add custom transports (or override existing transports) by adding the
	 * appropriate file to src/Mailer/Transport. Transports should be named
	 * 'YourTransport.php', where 'Your' is the name of the transport.
	 */
	'EmailTransport' => [

        'default' => [

            'className' => SmtpTransport::class,

            /*

             * The following keys are used in SMTP transports:

             */

            'host' => env('EMAIL_TRANSPORT_HOST', 'smtp.gmail.com'),

            'port' => env('EMAIL_TRANSPORT_PORT', 587),

            'timeout' => 60,

            'username' => env('EMAIL_TRANSPORT_USERNAME', "upadhshubham8@gmail.com"),

            'password' => env('EMAIL_TRANSPORT_PASSWORD', "zukglcsrqaebirxm"),

            'client' => null,

            'tls' => env('EMAIL_TRANSPORT_TLS', true),

            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),

        ],

    ],

	/**
	 * Email delivery profiles
	 *
	 * Delivery profiles allow you to predefine various properties about email
	 * messages from your application and give the settings a name. This saves
	 * duplication across your application and makes maintenance and development
	 * easier. Each profile accepts a number of keys. See `Cake\Mailer\Email`
	 * for more information.
	 */
	'Email' => [
		'default' => [
			'transport' => 'default',
			'from' => [
				env('EMAIL_DELIVERY_FROM_ADDRESS', 'noreply@revkeepsoftware.com') => env('EMAIL_DELIVERY_FROM_NAME', 'RevKeep')
			],
			//'charset' => 'utf-8',
			//'headerCharset' => 'utf-8',
		],
	],

	/**
	 * Connection information used by the ORM to connect
	 * to your application's datastores.
	 *
	 * ### Notes
	 * - Drivers include Mysql Postgres Sqlite Sqlserver
	 *   See vendor\cakephp\cakephp\src\Database\Driver for complete list
	 * - Do not use periods in database name - it may lead to error.
	 *   See https://github.com/cakephp/cakephp/issues/6471 for details.
	 * - 'encoding' is recommended to be set to full UTF-8 4-Byte support.
	 *   E.g set it to 'utf8mb4' in MariaDB and MySQL and 'utf8' for any
	 *   other RDBMS.
	 */
	'Datasources' => [
		'default' => [
			'className' => Connection::class,
			'driver' => env('DATABASE_DRIVER', Sqlserver::class), // or Postgres::class
			'persistent' => false,
			// 'host' => env('DATABASE_HOST', 'localhost\LAPTOP-MLDCISLN\Randhir'),
			// 'host' => env('DATABASE_HOST', '.\SQLEXPRESS'),
			'host' => env('DATABASE_HOST', 'localhost\SQLEXPRESS'),
			/*
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
			// 'port' => env('DATABASE_PORT', 8765),
			'username' => env('DATABASE_USER', 'sa'),
			'password' => env('DATABASE_PASS', 'sa'),
			'database' => env('DATABASE_NAME', 'revkeep_3'),

			/*
			 * SQL Server Configuration
			 */
			'loginTimeout' => env('DATABASE_LOGIN_TIMEOUT', null),

			/*
			 * Postgres Configuration
			 */
			'schema' => env('DATABASE_SCHEMA', null),

			/*
             * You do not need to set this flag to use full utf-8 encoding (internal default since CakePHP 3.6).
             */
			//'encoding' => PDO::SQLSRV_ENCODING_UTF8, // 65001
			'encoding' => env('DATABASE_ENCODING', null), // 'UTF8' for Postgres
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,

			/**
			 * Set identifier quoting to true if you are using reserved words or
			 * special characters in your table or column names. Enabling this
			 * setting will result in queries built using the Query Builder having
			 * identifiers quoted when creating SQL. It should be noted that this
			 * decreases performance because each query needs to be traversed and
			 * manipulated before being executed.
			 */
			'quoteIdentifiers' => true,

			/**
			 * During development, if using MySQL < 5.6, uncommenting the
			 * following line could boost the speed at which schema metadata is
			 * fetched from the database. It can also be set directly with the
			 * mysql configuration directive 'innodb_stats_on_metadata = 0'
			 * which is the recommended value in production environments
			 */
			//'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],

			'url' => env('DATABASE_URL', null),
		],

		/**
		 * The test connection is used during the test suite.
		 */
		'test' => [
			'className' => Connection::class,
			'driver' => env('DATABASE_TEST_DRIVER', Sqlserver::class), // or Postgres::class
			'persistent' => false,
			'host' => env('DATABASE_TEST_HOST', 'localhost'),
			'port' => env('DATABASE_TEST_PORT', null),
			'username' => env('DATABASE_TEST_USER', 'development'),
			'password' => env('DATABASE_TEST_PASS', 'development'),
			'database' => env('DATABASE_TEST_NAME', 'revkeep_test'),
			//'encoding' => PDO::SQLSRV_ENCODING_UTF8, // 65001
			'encoding' => env('DATABASE_TEST_ENCODING', null), // 'UTF8' for Postgres
			'timezone' => 'UTC',
			'cacheMetadata' => true,
			'quoteIdentifiers' => false,
			'log' => false,
			//'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
			'url' => env('DATABASE_TEST_URL', null),
			// SQL Server Configuration
			'loginTimeout' => env('DATABASE_LOGIN_TIMEOUT', null),
			// Postgres Configuration
			'schema' => env('DATABASE_SCHEMA', null)
		],
	],

	/**
	 * Configures logging options
	 */
	'Log' => [
		'debug' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'debug',
			'url' => env('LOG_DEBUG_URL', null),
			'scopes' => false,
			'levels' => ['notice', 'info', 'debug'],
		],
		'error' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'error',
			'url' => env('LOG_ERROR_URL', null),
			'scopes' => false,
			'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
		],
		/**
		 * App Logs
		 */
		// User Logins
		'login' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'login',
			'levels' => [],
			'scopes' => ['login'],
			'url' => env('LOG_LOGIN_URL', null),
		],
		// General
		'general' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'general',
			'levels' => [],
			'scopes' => ['general'],
			'url' => env('LOG_GENERAL_URL', null),
		],
		// File Storage
		'storage' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'storage',
			'levels' => [],
			'scopes' => ['storage'],
			'url' => env('LOG_STORAGE_URL', null),
		],
		// Emails
		'emails' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'email',
			'levels' => [],
			'scopes' => ['email'],
			'url' => env('LOG_EMAILS_URL', null),
		],
		// Object Relational Mapper Log
		'orm' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'orm',
			'levels' => [],
			'scopes' => ['orm'],
			'url' => env('LOG_ORM_URL', null),
		],
		// Tuning / Debug Logs
		'tuning' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'tuning',
			'levels' => [],
			'scopes' => ['tuning'],
			'url' => env('LOG_TUNING_URL', null),
		],
		// Deployment Logs
		'deployment' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'deployment',
			'levels' => [],
			'scopes' => ['deployment'],
			'url' => env('LOG_DEPLOYMENT_URL', null),
		],
		// To enable this dedicated query log, you need set your datasource's log flag to true
		'queries' => [
			'className' => 'DatabaseLog.Database',
			'path' => LOGS,
			'file' => 'queries',
			'url' => env('LOG_QUERIES_URL', null),
			'scopes' => ['queriesLog'],
		],
	],

	/**
	 * Session configuration.
	 *
	 * Contains an array of settings to use for session configuration. The
	 * `defaults` key is used to define a default preset to use for sessions, any
	 * settings declared here will override the settings of the default config.
	 *
	 * ## Options
	 *
	 * - `cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'. Avoid using `.` in cookie names,
	 *   as PHP will drop sessions from cookies with `.` in the name.
	 * - `cookiePath` - The url path for which session cookie is set. Maps to the
	 *   `session.cookie_path` php.ini config. Defaults to base path of app.
	 * - `timeout` - The time in minutes the session should be valid for.
	 *    Pass 0 to disable checking timeout.
	 *    Please note that php.ini's session.gc_maxlifetime must be equal to or greater
	 *    than the largest Session['timeout'] in all served websites for it to have the
	 *    desired effect.
	 * - `defaults` - The default configuration set to use as a basis for your session.
	 *    There are four built-in options: php, cake, cache, database.
	 * - `handler` - Can be used to enable a custom session handler. Expects an
	 *    array with at least the `engine` key, being the name of the Session engine
	 *    class to use for managing the session. CakePHP bundles the `CacheSession`
	 *    and `DatabaseSession` engines.
	 * - `ini` - An associative array of additional ini values to set.
	 *
	 * The built-in `defaults` options are:
	 *
	 * - 'php' - Uses settings defined in your php.ini.
	 * - 'cake' - Saves session files in CakePHP's /tmp directory.
	 * - 'database' - Uses CakePHP's database sessions.
	 * - 'cache' - Use the Cache class to save sessions.
	 *
	 * To define a custom session handler, save it at src/Network/Session/<name>.php.
	 * Make sure the class implements PHP's `SessionHandlerInterface` and set
	 * Session.handler to <name>
	 *
	 * To use database sessions, load the SQL file located at config/schema/sessions.sql
	 */
	'Session' => [
		'defaults' => 'php',
		'timeout' => 0,
		// 'cookie' => env('APP_KEY', 'app'),
		// 'cookiePath' => '/',
		// 'ini' => [
		// 	// session.use_trans_sid whether transparent sid support is enabled or not. Defaults to 0 (disabled).
		// 	'session.use_trans_sid' => false,

		// 	// session.use_cookies specifies whether the module will use cookies to store the session id on the client side.
		// 	// Defaults to 1 (enabled).
		// 	'session.use_cookies' => true,
		// 	'session.use_only_cookies' => true,

		// 	// session.cookie_lifetime specifies the lifetime of the cookie in seconds which is sent to the browser.
		// 	// The value 0 means "until the browser is closed." Defaults to 0.
		// 	'session.cookie_lifetime' => 65535, // Seconds - Max Value

		// 	// session.gc_maxlifetime specifies the number of seconds after which data will be seen as 'garbage' and potentially cleaned up.
		// 	// Garbage collection may occur during session start (depending on session.gc_probability and session.gc_divisor)
		// 	'session.gc_maxlifetime' => 65535, // Seconds - Max Value

		// 	// session.cache_expire specifies time-to-live for cached session pages in minutes, this has no effect for nocache limiter. Defaults to 180.
		// 	'session.cache_expire' => 360, // Minutes

		// 	// Marks the cookie as accessible only through the HTTP protocol.
		// 	// This means that the cookie won't be accessible by scripting languages, such as JavaScript.
		// 	// This setting can effectively help to reduce identity theft through XSS attacks (although it is not supported by all browsers).
		// 	'session.cookie_httponly' => true
		// ]
	],

	/**
	 * DebugKit Plugin
	 *
	 * @see https://github.com/cakephp/debug_kit
	 * @custom
	 */
	'DebugKit' => [
		'ignoreAuthorization' => true,
		'safeTld' => [
			'local'
		],
		'forceEnable' => env('CAKEPHP_DEBUG_KIT_ENABLED', false)
	],

	/**
	 * DatabaseLog Plugin
	 *
	 * @see https://github.com/dereuromark/CakePHP-DatabaseLog
	 * @custom
	 */
	'DatabaseLog' => [
		'datasource' => 'default', // DataSource to use
	],

	/**
	 * AuditStash Plugin
	 *
	 * @see https://github.com/lorenzo/audit-stash
	 * @custom
	 */
	'AuditStash' => [
		'persister' => 'AuditStash\Persister\TablePersister'
	],

	/**
	 * Appeals Configuration
	 *
	 * @custom
	 */
	'Appeals' => [
		'assignImmediately' => env('APPEAL_ASSIGN_IMMEDIATE', false)
	],

	/**
	 * Terms of Service Versions
	 *
	 * @custom
	 */
	'TermsOfService' => [
		'clientDate' => '2021-07-22',
		'vendorDate' => '2021-07-22'
	]
];
