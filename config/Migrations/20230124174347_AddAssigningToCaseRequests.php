<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAssigningToCaseRequests extends AbstractMigration
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
		$this->table('case_requests')
			->addColumn('assigned', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('assigned_to', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex(
				[
					'assigned_to',
				]
			)
			->save();
	}
}
