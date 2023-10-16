<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Client;

/**
 * License Service Provider Interface
 */
interface LicenseServiceInterface
{
	/**
	 * Return if licenses are enabled/enforced based on app configuration
	 *
	 * @return bool
	 */
	public function isEnabled(): bool;

	/**
	 * Get how many licenses a client is subscribed for
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return int
	 */
	public function getClientTotalLicenses(Client $client): int;

	/**
	 * Get how many licenses are left available for a client
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return int
	 */
	public function getClientAvailableLicenses(Client $client): int;

	/**
	 * Get how many licenses are used by a client
	 *
	 * @param \App\Model\Entity\Client $client
	 * @return int
	 */
	public function getClientUsedLicenses(Client $client): int;
}
