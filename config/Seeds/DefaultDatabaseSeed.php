<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Default Database Seed
 *
 * Calls other seeders in the correct order.
 */
class DefaultDatabaseSeed extends AbstractSeed
{
	/**
	 * Run Method
	 * @return void
	 */
	public function run(): void
	{
		// Clients
		$this->call('DefaultClientsSeed');

		// Client Data
		$clientSeeds = [
			'AppealLevelsSeed',
			'AppealTypesSeed',
			'CaseOutcomesSeed',
			'CaseTypesSeed',
			'DaysToRespondFromsSeed',
			'DenialTypesSeed',
			'FacilityTypesSeed',
			'InsuranceTypesSeed',
			'NotDefendableReasonsSeed',
			'ReferenceNumbersSeed',
			'RolesSeed',
			'WithdrawnReasonsSeed',
		];

		foreach ($clientSeeds as $seed) {
			$this->call($seed);
		}
	}
}
