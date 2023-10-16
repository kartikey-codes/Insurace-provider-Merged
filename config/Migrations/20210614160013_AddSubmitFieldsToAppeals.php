<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddSubmitFieldsToAppeals extends AbstractMigration
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
			->addColumn('cancelled', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('cancelled_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('submitted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('submitted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('closed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('closed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->save();
	}
}
