<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class ChangeClientSubscriptions extends AbstractMigration
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
			->drop()
			->save();

		$this->table('clients')
			->addColumn('client_subscription_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
				'signed' => false
			])
			->save();

		$this->table('client_subscriptions')
			->addColumn('provider_client_secret', 'string', [
				'default' => null,
				'limit' => 60,
				'null' => true
			])
			->save();
	}
}
