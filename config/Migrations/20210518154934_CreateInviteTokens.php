<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateInviteTokens extends AbstractMigration
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
		$this->table('invite_tokens')
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
			->addColumn('token', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => false,
			])
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('vendor_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => true,
				'limit' => null,
				'null' => true,
			])
			->save();
	}
}
