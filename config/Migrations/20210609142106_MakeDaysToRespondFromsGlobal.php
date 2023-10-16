<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeDaysToRespondFromsGlobal extends AbstractMigration
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
		$this->table('days_to_respond_froms')
			->dropForeignKey('client_id')
			->save();

		$this->table('days_to_respond_froms')
			->removeIndex(['client_id'])
			->save();

		$this->table('days_to_respond_froms')
			->removeColumn('client_id')
			->save();
	}
}
