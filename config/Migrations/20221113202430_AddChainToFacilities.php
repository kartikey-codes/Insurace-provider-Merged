<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddChainToFacilities extends AbstractMigration
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
		$this->table('facilities')
			->addColumn('chain_name', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->save();
	}
}
