<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFieldsToPatients extends AbstractMigration
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
		$this->table('patients')
			->addColumn('secured', 'boolean', [
				'default' => false,
				'limit' => null,
				'null' => false,
			])
			->addColumn('medical_record_number', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('ssn_last_four', 'string', [
				'default' => null,
				'limit' => 4,
				'null' => true,
			])
			->save();
	}
}
