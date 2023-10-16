<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFieldsToInsuranceProviders extends AbstractMigration
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
		$this->table('insurance_providers')
			->addColumn('website', 'string', [
				'default' => null,
				'limit' => 80,
				'null' => true,
			])
			->save();

		$this->table('appeal_types')
			->addColumn('short_name', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->save();

		$this->table('insurance_provider_appeal_types')
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
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_type_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addIndex(
				[
					'deleted',
				]
			)
			->addIndex(
				[
					'appeal_type_id',
				]
			)
			->addIndex(
				[
					'insurance_provider_id',
				]
			)
			->create();
	}
}
