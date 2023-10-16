<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddUtcToIncoming extends AbstractMigration
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
		$this->table('incoming_documents')
			->addColumn('unable_to_complete', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addIndex([
				'unable_to_complete'
			])
			->save();
	}
}
