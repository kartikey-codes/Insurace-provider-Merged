<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFacilitiesFields extends AbstractMigration
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
		$table = $this->table('facilities');

		$table->addColumn('npi_number', 'biginteger', [
			'default' => null,
			'null' => true,
		]);

		$table->addColumn('npi_manual', 'boolean', [
			'default' => null,
			'null' => true,
		]);

		$table->addColumn('primary_taxonomy', 'string', [
			'limit' => 50,
			'null' => true,
		]);

		$table->addColumn('client_owned', 'boolean', [
			'default' => null,
			'null' => true,
		]);

		$table->save();
	}
}
