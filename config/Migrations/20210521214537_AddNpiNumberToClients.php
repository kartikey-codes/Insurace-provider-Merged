<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddNpiNumberToClients extends AbstractMigration
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
		$this->table('clients')
			->addColumn('npi_number', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->save();
	}
}
