<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddLicensingToClientEmployees extends AbstractMigration
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
		$this->table('client_employees')
			->removeIndex(['emails_active'])
			->save();

		$this->table('client_employees')
			->addColumn('npi_number', 'biginteger', [
				'null' => true,
				'default' => null
			])
			->removeColumn('emails_active')
			->save();
	}
}
