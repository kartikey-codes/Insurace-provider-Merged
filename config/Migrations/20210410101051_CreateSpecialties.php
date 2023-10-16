<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateSpecialties extends AbstractMigration
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
		$table = $this->table('specialties');
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
		$table->create();

		$table = $this->table('vendor_specialties');
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
		$table->addColumn('vendor_id', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => false,
		]);
		$table->addColumn('specialty_id', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => false,
		]);
		$table->create();
	}
}
