<?php

declare(strict_types=1);

namespace App\Controller;

use App\Lib\AppHealthUtility\AppHealthUtility;
use Cake\Controller\Controller;

/**
 * Application Health Controller
 *
 * Handles routes related to health checks when running
 * in a container or in a non-development environment.
 */
class ApplicationHealthController extends Controller
{
	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		// Core 'RequestHandler' component
		$this->loadComponent('RequestHandler');

		// Force json responses
		$this->RequestHandler->renderAs($this, 'json');

		$this->setResponse(
			$this->getResponse()->withType('application/json')
		);

		$this->viewBuilder()->setOption('serialize', true);
	}

	/**
	 * Determine if the application is able to serve requests.
	 * Used by Dockerfile in the HEALTHCHECK option.
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->set('healthy', AppHealthUtility::isHealthy());
	}

	/**
	 * Prevents 404 errors in the application logs.
	 *
	 * Azure uses a dummy URL of /robots933456.txt to determine if a
	 * container is healthy and responding to requests. The output of this
	 * action doesn't seem to matter to Azure.
	 *
	 * @see https://github.com/MicrosoftDocs/azure-docs/blob/main/includes/app-service-web-configure-robots933456.md
	 * @return void
	 */
	public function dummyRobots(): void
	{
		$this->set('healthy', true);
	}
}
