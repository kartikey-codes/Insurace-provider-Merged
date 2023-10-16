<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddActiveToCaseTypes extends AbstractMigration
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
		$this->table('case_types')
			->addColumn('active', 'boolean', [
				'default' => true,
				'limit' => null,
				'null' => true
			])
			->save();

		// $this->execute('UPDATE [case_types] SET [active] = 1 WHERE [active] IS NULL;');
		//$this->execute('UPDATE case_types SET active = true WHERE active IS NULL;');

		$this->getQueryBuilder()->update('case_types')->set('active', true)->whereNull('active')->execute();
	}
}
