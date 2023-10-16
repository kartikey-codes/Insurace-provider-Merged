<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCaseRequests extends AbstractMigration
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
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
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
				'null' => false,
			])
			->addColumn('request_type', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->addColumn('unable_to_complete', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('due_date', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed_at', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('completed_by', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex([
				'case_id'
			])
			->addIndex([
				'client_id'
			])
			->addIndex([
				'request_type'
			])
			->addIndex([
				'unable_to_complete'
			])
			->addIndex([
				'completed'
			])
			->save();
	}
}
