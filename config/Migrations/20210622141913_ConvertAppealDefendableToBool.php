<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class ConvertAppealDefendableToBool extends AbstractMigration
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
		$this->table('appeals')
			->removeIndex(['defendable'])
			->save();

		$this->table('appeals')
			->changeColumn('defendable', 'boolean', [
				'null' => true,
				'default' => null
			])
			->save();
	}
}
