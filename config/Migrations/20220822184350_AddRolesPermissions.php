<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddRolesPermissions extends AbstractMigration
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
		$this->table('roles')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
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
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'name',
				]
			)
			->create();

		$this->table('permissions')
			->addColumn('controller', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('action', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => false,
			])
			->addIndex(
				[
					'controller',
				]
			)
			->addIndex(
				[
					'name',
				]
			)
			->create();

		$this->table('roles_permissions', ['id' => false, 'primary_key' => ['role_id', 'permission_id']])
			->addColumn('role_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('permission_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->create();

		$this->table('users_roles', ['id' => false, 'primary_key' => ['user_id', 'role_id']])
			->addColumn('user_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('role_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->create();
	}
}
