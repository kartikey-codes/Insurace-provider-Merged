<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAgenciesAgain extends AbstractMigration
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
		$this->table('agencies')
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
			->addColumn('client_id', 'integer', [
				'default' => 0,
				'limit' => 10,
				'null' => false,
				'signed' => false
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('division', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('active', 'boolean', [
				'default' => true,
				'limit' => null,
				'null' => false,
			])
			->addColumn('third_party_contractor', 'boolean', [
				'default' => true,
				'limit' => null,
				'null' => false,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('website', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addColumn('street_address_1', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('street_address_2', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('city', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('state', 'string', [
				'default' => null,
				'limit' => 2,
				'null' => true,
			])
			->addColumn('zip', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->addColumn('contact_name', 'string', [
				'default' => null,
				'limit' => 150,
				'null' => true,
			])
			->addColumn('contact_title', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('contact_email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('contact_phone', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('contact_fax', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('outgoing_primary_method', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addIndex(
				[
					'active',
				]
			)
			->addIndex(
				[
					'third_party_contractor',
				]
			)
			->addIndex(
				[
					'client_id',
				]
			)
			->addIndex(
				[
					'deleted',
				]
			)
			->create();

		$this->table('agency_outgoing_profile')
			->addColumn('agency_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('fax_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_name', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_department', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_address_1', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_address_2', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_city', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_state', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('mail_to_zip', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('electronic_website', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->addIndex(
				[
					'agency_id',
				]
			)
			->create();
	}
}
