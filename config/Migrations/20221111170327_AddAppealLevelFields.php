<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAppealLevelFields extends AbstractMigration
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
		$this->table('appeal_levels')
			->addColumn('short_name', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true,
			])
			->addColumn('description', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('is_request', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('is_scheduled', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->save();
	}
}
