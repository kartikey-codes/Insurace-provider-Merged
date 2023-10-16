<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddClientPaymentSources extends AbstractMigration
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
		$this->table('client_payment_sources')
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
			->addColumn('provider_name', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true
			])
			->addColumn('provider_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('brand', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('fingerprint', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('last4', 'string', [
				'default' => null,
				'limit' => 6,
				'null' => true
			])
			->addColumn('meta_data', 'text', [
				'default' => null,
				'null' => true
			])
			->addIndex([
				'client_id'
			])
			->addIndex([
				'provider_name'
			])
			->addIndex([
				'provider_id'
			])
			->save();
	}
}
