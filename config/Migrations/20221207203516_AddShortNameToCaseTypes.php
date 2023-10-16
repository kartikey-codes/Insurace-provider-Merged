<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddShortNameToCaseTypes extends AbstractMigration
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
			->addColumn('short_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->save();
	}
}
