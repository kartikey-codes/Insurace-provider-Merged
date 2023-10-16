<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddUtcFields extends AbstractMigration
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
		$this->table('cases')
			->addColumn('unable_to_complete', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->save();

		$this->table('appeals')
			->addColumn('unable_to_complete', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->save();
	}
}
