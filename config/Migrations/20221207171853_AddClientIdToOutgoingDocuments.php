<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddClientIdToOutgoingDocuments extends AbstractMigration
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
		$this->table('outgoing_documents')->truncate();

		$this->table('outgoing_documents')
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex([
				'client_id'
			])
			->addForeignKey('client_id', 'clients', 'id')
			->save();
	}
}
