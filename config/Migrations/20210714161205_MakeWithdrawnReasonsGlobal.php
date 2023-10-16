<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeWithdrawnReasonsGlobal extends AbstractMigration
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
		$this->table('withdrawn_reasons')
			->removeIndex(['client_id'])
			->save();

		$this->table('withdrawn_reasons')
			->dropForeignKey('client_id')
			->removeColumn('client_id')
			->save();
	}
}
