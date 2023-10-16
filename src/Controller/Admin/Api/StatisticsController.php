<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Cake\Core\Configure;

/**
 * Statistics Controller
 */
class StatisticsController extends ApiController
{
	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->request->allowMethod(['get']);

		$memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

		$app = [
			'Name' => Configure::readOrFail('App.name'),
			'Assign Appeals Immediately' => Configure::read('Appeals.assignImmediately') ? 'Yes' : 'No (Queued)',
			'Registration' => Configure::readOrFail('Registration.enabled') ? 'Enabled' : 'Disabled',
			'NPI Validation' => Configure::readOrFail('NPI.validateClients') ? 'Yes' : 'No',
		];

		$php = [
			'PHP Version' => PHP_VERSION,
			'CakePHP Version' => Configure::version(),
			'Memory' => sprintf('%s / %s', $memoryUsage . 'MB', ini_get('memory_limit')),
			'Session Handler' => Configure::read('Session.defaults'),
			'Max Upload' => ini_get('upload_max_filesize'),
			'Max POST' => ini_get('post_max_size'),
		];

		$drivers = [
			'Subscription' => Configure::read('Subscriptions.driver'),
			'Storage' => Configure::Read('Storage.driver'),
		];

		$this->set(compact(
			'app',
			'php',
			'drivers'
		));
	}
}
