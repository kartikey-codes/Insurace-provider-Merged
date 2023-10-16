<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\NpiServiceInterface;
use Cake\Cache\Cache;
use Cake\Event\EventInterface;

/**
 * NPI Lookup Controller
 *
 * Handles searching the NPI registry when registering a
 * new client. Aids filling in fields for client.
 *
 * @property \App\Controller\Component\LogComponent $Log
 */
class NpiLookupController extends AppController
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

		// App 'Log' Component
		$this->loadComponent('Log');

		// Request Handler
		$this->loadComponent('RequestHandler');
	}

	/**
	 * Before filter callback.
	 * Executes before every request.
	 *
	 * @param \Cake\Event\Event $event The beforeFilter event.
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void
	{
		parent::beforeFilter($event);
	}

	/**
	 * Before render callback.
	 * Executes before the view is rendered
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		// Respond as JSON
		$this->RequestHandler->renderAs($this, 'json');
		$this->setResponse(
			$this->getResponse()->withType('application/json')
		);

		$this->viewBuilder()->setOption('serialize', ['results']);
		$this->viewBuilder()->setLayout('login');
	}

	/**
	 * NPI Lookup for new client verification
	 *
	 * @param \App\Service\NpiServiceInterface $npiService
	 * @return void
	 */
	public function index(NpiServiceInterface $npiService): void
	{
		// Get form values
		$companyName = $this->getRequest()->getQuery('name');
		$companyState = $this->getRequest()->getQuery('state');

		// Require a company name to search NPI API
		if (empty($companyName)) {
			$this->setResponse($this->getResponse()->withStatus(400));
			$this->set('error', __('A company name must be provided.'));

			return;
		}

		// Make a cache for this input
		$cacheKey = $this->generateCacheKey($companyName, $companyState);
		$results = Cache::read($cacheKey, 'npi');

		if ($results === false || $results === null) {
			$results = $this->searchOrganization($npiService, $companyName, $companyState);
			Cache::write($cacheKey, $results, 'npi');
		}

		$this->set(compact('results'));
	}

	/**
	 * Perform search using NPI Service
	 *
	 * @param \App\Service\NpiServiceInterface $npiService
	 * @param string $companyName
	 * @param string $companyState
	 * @return array
	 */
	private function searchOrganization(NpiServiceInterface $npiService, string $companyName, string $companyState): array
	{
		return $npiService->searchOrganizationByNameAndState($companyName, $companyState);
	}

	/**
	 * Generate a cache key for a combination of name/state
	 *
	 * @param string $companyName
	 * @param string $companyState
	 * @return string
	 */
	private function generateCacheKey(string $companyName, string $companyState): string
	{
		$sanitizedName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', strtolower($companyName));
		$sanitizedState = preg_replace('/[^a-zA-Z0-9_-]+/', '_', strtolower($companyState));

		return $sanitizedName . '_' . $sanitizedState;
	}
}
