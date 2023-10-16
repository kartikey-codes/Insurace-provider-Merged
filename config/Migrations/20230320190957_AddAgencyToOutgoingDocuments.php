<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAgencyToOutgoingDocuments extends AbstractMigration
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
		$this->table('outgoing_documents')
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
