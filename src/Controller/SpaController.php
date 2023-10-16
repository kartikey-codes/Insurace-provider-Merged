<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

class SpaController extends AppController
{
	/**
	 * Initialize
	 *
	 * Set up any components or things we need this controller to do during startup
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		$this->viewBuilder()
			->disableAutoLayout();

		$this->set('_debugMode', Configure::read('debug', false));
	}

	/**
	 * Client Single Page App
	 *
	 * @return void
	 */
	public function client(): void
	{
	}

	/**
	 * Vendor Single Page App
	 *
	 * @return void
	 */
	public function vendor(): void
	{
	}

	/**
	 * Admin Single Page App
	 *
	 * @return void
	 */
	public function admin(): void
	{
	}
}
