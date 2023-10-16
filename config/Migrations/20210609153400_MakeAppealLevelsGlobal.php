<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeAppealLevelsGlobal extends AbstractMigration
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
		$this->table('appeal_levels')
			->dropForeignKey('client_id')
			->save();

		$this->table('appeal_levels')
			->removeIndex(['client_id'])
			->save();

		$this->table('appeal_levels')
			->removeColumn('client_id')
			->save();
	}
}
