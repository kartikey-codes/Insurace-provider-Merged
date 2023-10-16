<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFacilityServices extends AbstractMigration
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
		$this->table('facilities_services', ['id' => false, 'primary_key' => ['facility_id', 'service_id']])
			->addColumn('facility_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('service_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->create();
	}
}
