<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddVendorUserFields extends AbstractMigration
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
			->addColumn('owner_user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true
			])
			->addIndex('owner_user_id')
			->save();

		$this->table('users')
			->addColumn('vendor_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true
			])
			->addIndex('vendor_id')
			->save();
	}
}
