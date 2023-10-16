<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddKeyToPermissions extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 * @return void
	 */
	public function change(): void
	{
		$this->table('permissions')
			->truncate();

		$this->table('permissions')
			->addColumn('key', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addIndex([
				'key'
			], [
				'unique' => true
			])
			->save();
	}
}
