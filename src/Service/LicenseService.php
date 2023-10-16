<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Client;
use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Licensing
 */
class LicenseService implements LicenseServiceInterface
{
	use LocatorAwareTrait;

	/**
	 * @inheritDoc
	 */
	public function isEnabled(): bool
	{
		$enabled = (bool)Configure::readOrFail('Subscriptions.licensingEnabled');

		return $enabled;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientTotalLicenses(Client $client): int
	{
		return !empty($client->licenses) ? intval($client->licenses) : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientAvailableLicenses(Client $client): int
	{
		/**
		 * @var int $usedLicenses
		 */
		$usedLicenses = $this->getClientUsedLicenses($client);

		/**
		 * @var int $totalLicenses
		 */
		$totalLicenses = $this->getClientTotalLicenses($client);

		/**
		 * @var int $availableLicenses
		 */
		$availableLicenses = $totalLicenses - $usedLicenses;

		return $availableLicenses >= 0 ? $availableLicenses : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientUsedLicenses(Client $client): int
	{
		/**
		 * @var \App\Model\Table\ClientEmployeesTable $clientEmployees
		 */
		$clientEmployees = $this->fetchTable('ClientEmployees');

		$count = $clientEmployees->find('all', ['skipTenantCheck' => true])
			->where([
				'ClientEmployees.client_id' => $client->id,
			])
			->count();

		return $count;
	}
}
