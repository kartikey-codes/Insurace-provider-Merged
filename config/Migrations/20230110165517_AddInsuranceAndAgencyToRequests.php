<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddInsuranceAndAgencyToRequests extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 * @return void
	 */
	public function change(): void
	{
		$this->table('case_requests')
			->addColumn('agency_id', 'integer', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addIndex([
				'agency_id'
			])
			->addIndex([
				'insurance_provider_id'
			])
			->save();
	}
}
