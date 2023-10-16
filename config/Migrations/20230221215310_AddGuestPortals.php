<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddGuestPortals extends AbstractMigration
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
		$this->table('guest_portals')
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
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
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->addColumn('recipient_name', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->addColumn('description', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('last_accessed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('expiration_date', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed_at', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex([
				'case_id'
			])
			->addIndex([
				'client_id'
			])
			->addIndex([
				'completed'
			])
			->save();
	}
}
