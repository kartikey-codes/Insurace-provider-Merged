<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAgencyToAppeals extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 * @return void
	 */
	public function change(): void
	{
		$this->table('appeals')
			->addColumn('agency_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'agency_id',
				]
			)
			->save();
	}
}
