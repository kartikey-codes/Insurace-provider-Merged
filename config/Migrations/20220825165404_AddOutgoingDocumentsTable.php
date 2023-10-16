<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddOutgoingDocumentsTable extends AbstractMigration
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
		$this->table('outgoing_documents')
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('modified', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('modified_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('deleted', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('deleted_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('appeal_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('filename', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
			->addColumn('delivery_method', 'string', [
				'default' => null,
				'limit' => 250,
				'null' => true,
			])
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
			->addColumn('processed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('failed', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('status_message', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addIndex(
				[
					'cancelled',
				]
			)
			->addIndex(
				[
					'completed',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'failed',
				]
			)
			->addIndex(
				[
					'case_id',
				]
			)
			->addIndex(
				[
					'appeal_id',
				]
			)
			->addIndex(
				[
					'cancelled_by',
				]
			)
			->addIndex(
				[
					'delivery_method',
				]
			)
			->create();
	}
}
