<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveInsuranceProviderOpportunities extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 * @return void
	 */
	public function change()
	{
		$this->table('insurance_provider_opportunity_sets')
			->drop()
			->save();

		$this->table('insurance_provider_opportunities')
			->drop()
			->save();
	}
}
