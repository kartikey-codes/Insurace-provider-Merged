<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveExtraFieldsFromIncomingDocuments extends AbstractMigration
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
		$this->table('incoming_documents')
			->removeIndex(['provider_identifier'])
			->save();

		$this->table('incoming_documents')
			->removeColumn('sender_number')
			->removeColumn('pages')
			->removeColumn('provider_identifier')
			->removeColumn('acknowledgement_sent')
			->save();
	}
}
