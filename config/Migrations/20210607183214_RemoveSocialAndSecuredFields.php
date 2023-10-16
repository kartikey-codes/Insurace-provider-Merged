<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveSocialAndSecuredFields extends AbstractMigration
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
		$this->table('patients')
			->removeIndex(['secured'])
			->save();

		$this->table('patients')
			->removeColumn('social')
			->removeColumn('secured')
			->save();
	}
}
