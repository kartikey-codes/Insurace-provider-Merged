<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeCaseOutcomesGlobal extends AbstractMigration
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
		$this->table('case_outcomes')
			->dropForeignKey('client_id')
			->save();

		$this->table('case_outcomes')
			->removeIndex(['client_id'])
			->save();

		$this->table('case_outcomes')
			->removeColumn('client_id')
			->save();
	}
}
