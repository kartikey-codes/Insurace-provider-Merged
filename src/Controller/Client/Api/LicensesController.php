<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\TenantUtility\TenantUtility;
use App\Model\Entity\Client;
use App\Model\Table\ClientsTable;
use App\Service\LicenseServiceInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Client Licenses Controller
 *
 * @property \App\Model\Table\Clients $Clients
 */
class LicensesController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var int|null
	 */
	private $clientId;

	/**
	 * @var \App\Model\Entity\Client
	 */
	private Client $client;

	/**
	 * @var \App\Model\Table\ClientsTable
	 */
	public ClientsTable $Clients;

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

		$this->Clients = $this->fetchTable('Clients');
		$this->getClient();
	}

	/**
	 * Get the Client entity from the request
	 *
	 * @return \App\Model\Entity\Client
	 */
	private function getClient(): Client
	{
		$this->clientId = TenantUtility::getTenantIdFromRequest();
		$this->client = $this->Clients->get($this->clientId);

		return $this->client;
	}

	/**
	 * List Subscriptions
	 * @param LicenseServiceInterface $licenseService
	 * @return void
	 */
	public function index(LicenseServiceInterface $licenseService): void
	{
		$this->set('data', [
			'enabled' => $licenseService->isEnabled(),
			'total' => $licenseService->getClientTotalLicenses($this->client),
			'available' => $licenseService->getClientAvailableLicenses($this->client),
			'used' => $licenseService->getClientUsedLicenses($this->client),
		]);
	}
}
