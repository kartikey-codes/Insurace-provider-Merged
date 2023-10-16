<?php

declare(strict_types=1);

namespace App\Service;

/**
 * NPI Service Interface
 */
interface NpiServiceInterface
{
	/**
	 * Validate an organization NPI number exists in the NPI Registry
	 *
	 * @param int $npiNumber
	 * @return bool
	 */
	public function isValidOrganization(int $npiNumber): bool;

	/**
	 * Validate an individual person's NPI number exists in the NPI Registry
	 *
	 * @param int $npiNumber
	 * @return bool
	 */
	public function isValidIndividual(int $npiNumber): bool;

	/**
	 * Lookup by name for organization matches in the NPI Registry
	 *
	 * @param string $name
	 * @param bool $exact
	 * @return array<\App\Lib\NpiUtility\NpiOrganizationResult>
	 */
	public function searchOrganizationByName(string $name, bool $exact = false): array;

	/**
	 * Lookup by name for organization matches in the NPI Registry with state filter
	 *
	 * @param string $name
	 * @param string $state
	 * @param bool $exact
	 * @return array<\App\Lib\NpiUtility\NpiOrganizationResult>
	 */
	public function searchOrganizationByNameAndState(string $name, string $state, bool $exact = false): array;

	/**
	 * Lookup by NPI number for organization matches in the NPI Registry
	 *
	 * @param int $npiNumber
	 * @return array<\App\Lib\NpiUtility\NpiOrganizationResult>
	 */
	public function searchOrganizationByNumber(int $npiNumber): array;

	/**
	 * Lookup by name for individual person matches in the NPI Registry
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param bool $exact
	 * @return array<\App\Lib\NpiUtility\NpiIndividualResult>
	 */
	public function searchIndividualByName(string $firstName, string $lastName, bool $exact = true): array;

	/**
	 * Lookup by name and state for individual person matches in the NPI Registry
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $state
	 * @param bool $exact
	 * @return array<\App\Lib\NpiUtility\NpiIndividualResult>
	 */
	public function searchIndividualByNameAndState(string $firstName, string $lastName, string $state, bool $exact = true): array;

	/**
	 * Lookup by NPI number for individual person matches in the NPI Registry
	 *
	 * @param int $npiNumber
	 * @return array<\App\Lib\NpiUtility\NpiIndividualResult>
	 */
	public function searchIndividualByNumber(int $npiNumber): array;
}
