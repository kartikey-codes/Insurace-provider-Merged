<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddUtcReasons extends AbstractMigration
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
		$this->table('utc_reasons')
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
				'limit' => 250,
				'null' => true,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex([
				'client_id'
			])
			->addForeignKey('client_id', 'clients', 'id')
			->create();

		$this->table('appeal_utc_reasons', ['id' => false, 'primary_key' => ['appeal_id', 'utc_reason_id']])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('utc_reason_id', 'integer', [
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
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'utc_reason_id',
				]
			)
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex([
				'client_id'
			])
			->addForeignKey('client_id', 'clients', 'id')
			->create();
	}
}
