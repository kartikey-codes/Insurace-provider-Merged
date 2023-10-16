<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class ImproveRolesAndPermissions extends AbstractMigration
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
		$this->table('roles_permissions')->truncate();

		$this->table('roles_permissions')
			->addColumn('client_id', 'integer', [
				'limit' => 10,
				'null' => false,
			])
			->addIndex([
				'client_id'
			])
			->addForeignKey('client_id', 'clients', 'id')
			->save();

		$this->table('roles')->truncate();

		$this->table('roles')
			->addColumn('client_id', 'integer', [
				'limit' => 10,
				'null' => false,
			])
			->addIndex([
				'client_id'
			])
			->addForeignKey('client_id', 'clients', 'id')
			->save();

		$this->table('users')
			->addColumn('client_admin', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => false,
			])
			->addIndex([
				'client_admin'
			])
			->save();
	}
}
