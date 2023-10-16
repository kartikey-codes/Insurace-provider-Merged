<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeReferenceNumbersGlobal extends AbstractMigration
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
		$this->table('reference_numbers')
			->dropForeignKey('client_id')
			->save();

		$this->table('reference_numbers')
			->removeIndex(['client_id'])
			->save();

		$this->table('reference_numbers')
			->removeColumn('client_id')
			->save();
	}
}
