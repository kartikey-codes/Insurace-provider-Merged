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
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\Type\StringType;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.default to `config/.env` and set/modify the
 * variables as required.
 */
if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
	$overwriteEnv = true; // Throws logic exception about debug already being defined without this
	$dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
	$dotenv->parse()
		->putenv($overwriteEnv)
		->toEnv()
		->toServer();
}

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
	Configure::config('default', new PhpConfig());

	// Application Required
	Configure::load('app', 'default', false);

	// Other Configurations
	Configure::load('adobe', 'default'); // @custom
	Configure::load('cakepdf', 'default'); // @custom @plugin
	Configure::load('company', 'default'); // @custom
	Configure::load('login', 'default'); // @custom
	Configure::load('multitenancy', 'default'); // @custom
	Configure::load('npi', 'default'); // @custom
	Configure::load('passwords', 'default'); // @custom
	Configure::load('registration', 'default'); // @custom
	Configure::load('ssl', 'default'); // @custom
	Configure::load('storage', 'default'); // @custom
	Configure::load('subscriptions', 'default'); // @custom
	Configure::load('vendors', 'default'); // @custom
} catch (\Exception $e) {
	exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file to provide overrides to your configuration.
 * Notice: For security reasons app_local.php **should not** be included in your git repo.
 */
if (file_exists(CONFIG . 'app_local.php')) {
	Configure::load('app_local', 'default');
}

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
	Configure::write('Cache._cake_model_.duration', '+2 minutes');
	Configure::write('Cache._cake_core_.duration', '+2 minutes');
	// disable router cache during development
	Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
(new ErrorTrap(Configure::read('Error')))->register();
(new ExceptionTrap(Configure::read('Error')))->register();

/*
 * Include the CLI bootstrap overrides.
 */
if (PHP_SAPI === 'cli') {
	require CONFIG . 'bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 */
$fullBaseUrl = Configure::read('App.fullBaseUrl');
if (!$fullBaseUrl) {
	$s = null;
	if (env('HTTPS')) {
		$s = 's';
	}

	$httpHost = env('HTTP_HOST');
	if (isset($httpHost)) {
		$fullBaseUrl = 'http' . $s . '://' . $httpHost;
	}
	unset($httpHost, $s);
}
if ($fullBaseUrl) {
	Router::fullBaseUrl($fullBaseUrl);
}
unset($fullBaseUrl);

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Mailer::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
	$detector = new \Detection\MobileDetect();

	return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
	$detector = new \Detection\MobileDetect();

	return $detector->isTablet();
});

/*
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/4/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
// \Cake\Database\TypeFactory::build('time')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('date')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('datetime')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('timestamp')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('datetimefractional')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('timestampfractional')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('datetimetimezone')
//    ->useLocaleParser();
// \Cake\Database\TypeFactory::build('timestamptimezone')
//    ->useLocaleParser();

// There is no time-specific type in Cake
TypeFactory::map('time', StringType::class);

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);

/**
 * Application Specific Code
 *
 * Here's everything not included with CakePHP.
 *
 * @custom @config
 */

// Pagination
define('PAGINATION_LOW_LIMIT', env('PAGINATION_LOW_LIMIT', 5));
define('PAGINATION_DEFAULT_LIMIT', env('PAGINATION_DEFAULT_LIMIT', 15));
define('PAGINATION_HIGH_LIMIT', env('PAGINATION_HIGH_LIMIT', 50));

// Area detectors
ServerRequest::addDetector('admin', function ($request) {
	$isSpa = $request->getParam('controller') == 'Spa' && $request->getParam('action') == 'admin';
	$isPrefix = $request->getParam('prefix') == 'Admin' || $request->getParam('prefix') == 'Admin/Api';
	return $isSpa || $isPrefix;
});

ServerRequest::addDetector('client', function ($request) {
	$isSpa = $request->getParam('controller') == 'Spa' && $request->getParam('action') == 'client';
	$isPrefix = $request->getParam('prefix') == 'Client' || $request->getParam('prefix') == 'Client/Api';
	return $isSpa || $isPrefix;
});

ServerRequest::addDetector('vendor', function ($request) {
	$isSpa = $request->getParam('controller') == 'Spa' && $request->getParam('action') == 'vendor';
	$isPrefix = $request->getParam('prefix') == 'Vendor' || $request->getParam('prefix') == 'Vendor/Api';
	return $isSpa || $isPrefix;
});
