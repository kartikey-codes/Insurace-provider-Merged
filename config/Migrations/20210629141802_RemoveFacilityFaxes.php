<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveFacilityFaxes extends AbstractMigration
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
		$this->table('facility_faxes')
			->removeIndex(['client_id'])
			->save();

		$this->table('facility_faxes')
			->dropForeignKey('client_id')
			->save();

		$this->table('facility_faxes')
			->drop()
			->save();
	}
}
