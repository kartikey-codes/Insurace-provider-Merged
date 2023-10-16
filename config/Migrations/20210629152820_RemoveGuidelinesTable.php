<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveGuidelinesTable extends AbstractMigration
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
		$this->table('guidelines')
			->removeIndex(['client_id'])
			->save();

		$this->table('guidelines')
			->dropForeignKey('client_id')
			->save();

		$this->table('guidelines')
			->drop()
			->save();
	}
}
