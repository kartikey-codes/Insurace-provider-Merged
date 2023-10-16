<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveDenialAgencies extends AbstractMigration
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
		$this->table('insurance_provider_agencies')
			->dropForeignKey('client_id')
			->drop()
			->save();

		$this->table('agencies')
			->dropForeignKey('client_id')
			->drop()
			->save();

		$this->table('appeals')
			->removeIndex(['agency_id'])
			->save();

		$this->table('appeals')
			->removeColumn('agency_id')
			->save();
	}
}
