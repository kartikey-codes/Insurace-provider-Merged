<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddClientEmployeeToCases extends AbstractMigration
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
			->addColumn('state', 'string', [
				'limit' => 50,
				'null' => true,
			])
			->save();

		$this->table('cases')
			->addColumn('client_employee_id', 'integer', [
				'null' => true,
				'default' => null
			])
			->save();
	}
}
