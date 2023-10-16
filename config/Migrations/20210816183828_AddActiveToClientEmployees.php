<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddActiveToClientEmployees extends AbstractMigration
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
			->addColumn('active', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->save();

		//$this->execute("UPDATE client_employees SET active = true WHERE active IS NULL;");

		$this->getQueryBuilder()->update('client_employees')->set('active', true)->whereNull('active')->execute();
	}
}
