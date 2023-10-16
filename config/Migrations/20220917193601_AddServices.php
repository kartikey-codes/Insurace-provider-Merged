<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddServices extends AbstractMigration
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
		$this->table('services')
			->addColumn('client_id', 'integer', [
				'default' => 0,
				'limit' => 10,
				'null' => false,
				'signed' => false
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
				'signed' => false
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
				'signed' => false
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true
			])
			->addColumn('description', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'null' => false
			])
			->addIndex([
				'client_id'
			])
			->addIndex([
				'active'
			])
			->save();
	}
}
