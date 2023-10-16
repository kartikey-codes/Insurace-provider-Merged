<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateVendors extends AbstractMigration
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
		$table = $this->table('vendors');
		$table->addColumn('created', 'timestamp', [
			'default' => null,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('created_by', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('modified', 'timestamp', [
			'default' => null,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('modified_by', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('deleted', 'timestamp', [
			'default' => null,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('deleted_by', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('name', 'string', [
			'default' => null,
			'limit' => 255,
			'null' => false,
		]);
		$table->addColumn('street_address_1', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('street_address_2', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('city', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('state', 'string', [
			'default' => null,
			'limit' => 2,
			'null' => true,
		]);
		$table->addColumn('zip', 'string', [
			'default' => null,
			'limit' => 20,
			'null' => true,
		]);
		$table->addColumn('phone', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('owner_user_id', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => false,
		]);
		$table->addColumn('active', 'boolean', [
			'default' => false,
			'limit' => null,
			'null' => true,
		]);
		$table->create();
	}
}
