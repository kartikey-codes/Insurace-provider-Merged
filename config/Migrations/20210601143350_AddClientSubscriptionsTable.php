<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddClientSubscriptionsTable extends AbstractMigration
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
			->addColumn('payment_provider_customer_id', 'string', [
				'default' => null,
				'null' => true
			])
			->save();

		$this->table('client_subscriptions')
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
			->addColumn('recurring_interval', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true
			])
			->addColumn('recurring_price', 'decimal', [
				'default' => null,
				'null' => false,
				'precision' => 5,
				'scale' => 2,
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'null' => false
			])
			->addColumn('expires_at', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('provider_name', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true
			])
			->addColumn('provider_customer_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('provider_product_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('provider_price_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('provider_subscription_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('provider_status', 'string', [
				'default' => null,
				'limit' => 30,
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
				'provider_customer_id'
			])
			->addIndex([
				'provider_product_id'
			])
			->addIndex([
				'provider_price_id'
			])
			->addIndex([
				'provider_subscription_id'
			])
			->addIndex([
				'provider_status'
			])
			->addIndex([
				'active'
			])
			->save();
	}
}
