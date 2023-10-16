<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class SimplifySubscriptions extends AbstractMigration
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
		$this->table('client_subscriptions')
			->drop()
			->save();

		$this->table('clients')
			->removeColumn('client_subscription_id')
			->addColumn('payment_provider_name', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('payment_provider_subscription_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->addColumn('subscription_product_id', 'string', [
				'default' => null,
				'limit' => 30,
				'null' => true
			])
			->save();
	}
}
