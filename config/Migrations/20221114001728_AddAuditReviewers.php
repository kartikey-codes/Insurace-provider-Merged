<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAuditReviewers extends AbstractMigration
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
		$this->table('audit_reviewers')
			->addColumn('client_id', 'integer', [
				'default' => 0,
				'limit' => 10,
				'null' => false,
				'signed' => false
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
				'signed' => false
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
				'signed' => false
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
				'signed' => false
			])
			->addColumn('agency_id', 'integer', [
				'default' => 0,
				'limit' => 10,
				'null' => false,
				'signed' => false
			])
			->addColumn('first_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('middle_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('last_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('title', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('professional_degree', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true
			])
			->addColumn('notes', 'text', [
				'default' => null,
				'null' => true
			])
			->addColumn('active', 'boolean', [
				'default' => false,
				'null' => false
			])
			->addIndex([
				'client_id'
			])
			->addIndex([
				'agency_id'
			])
			->addIndex([
				'active'
			])
			->save();
	}
}
