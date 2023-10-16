<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeClientEmployeeTypesGlobal extends AbstractMigration
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
		$this->table('client_employee_types')
			->removeIndex(['client_id'])
			->save();

		$this->table('client_employee_types')
			->dropForeignKey('client_id')
			->removeColumn('client_id')
			->save();
	}
}
