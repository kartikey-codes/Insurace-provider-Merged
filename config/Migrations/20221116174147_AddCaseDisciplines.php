<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCaseDisciplines extends AbstractMigration
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
		$this->table('cases_disciplines', ['id' => false, 'primary_key' => ['case_id', 'discipline_id']])
			->addColumn('case_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('discipline_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addIndex([
				'client_id'
			])
			->addForeignKey('client_id', 'clients', 'id')
			->create();
	}
}
