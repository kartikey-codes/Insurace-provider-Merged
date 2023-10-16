<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFieldsToClients extends AbstractMigration
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
		$table = $this->table('client_types');
		$table->addColumn('created', 'timestamp', [
			'default' => null,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('created_by', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('modified', 'timestamp', [
			'default' => null,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('modified_by', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('deleted', 'timestamp', [
			'default' => null,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('deleted_by', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('name', 'string', [
			'default' => null,
			'limit' => 250,
			'null' => false,
		]);
		$table->create();

		$type1 = ['name' => 'Multi Healthcare Organization'];
		$type2 = ['name' => 'Single Healthcare Organization'];
		$type3 = ['name' => 'Non-Healthcare Organization'];

		$table->insert($type1);
		$table->insert($type2);
		$table->insert($type3);
		$table->saveData();

		$table = $this->table('clients');
		$table->addColumn('client_type_id', 'integer', [
			'default' => null,
			'limit' => 10,
			'null' => true,
		]);
		$table->addColumn('email', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('active', 'boolean', [
			'default' => true,
			'limit' => null,
			'null' => true,
		]);
		$table->addColumn('phone', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('fax', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('street_address_1', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('street_address_2', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('city', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('state', 'string', [
			'default' => null,
			'limit' => 2,
			'null' => true,
		]);
		$table->addColumn('zip', 'string', [
			'default' => null,
			'limit' => 20,
			'null' => true,
		]);
		$table->addColumn('contact_first_name', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('contact_last_name', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('contact_department', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('contact_title', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('contact_phone', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('contact_fax', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		$table->addColumn('contact_email', 'string', [
			'default' => null,
			'limit' => 50,
			'null' => true,
		]);
		// the following doesn't work when rollback
		//$table->addIndex(['client_type_id']);
		//$table->addIndex(['active']);
		$table->update();

		$this->getQueryBuilder()->update('clients')->set('active', true)->execute();

		//   $this->execute("UPDATE clients SET active=1");
		//$this->execute("UPDATE clients SET active=true");
	}
}
