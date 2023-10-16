<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddMoreFacilitiesFields extends AbstractMigration
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
		$this->table('facilities')
			->addColumn('area_name', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('ou_number', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('territory', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true,
			])
			->addColumn('rvp_name', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->addColumn('has_contract', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('contract_start_date', 'date', [
				'default' => null,
				'null' => true,
				'limit' => null
			])
			->addColumn('contract_end_date', 'date', [
				'default' => null,
				'null' => true,
				'limit' => null
			])
			->addColumn('indemnification_days', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('max_return_work_days', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->save();
	}
}
