<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddMetadataToInsuranceAppealLevels extends AbstractMigration
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
		$this->table('insurance_provider_appeal_levels')
			->drop()
			->save();

		$this->table('insurance_provider_appeal_levels', ['id' => false, 'primary_key' => ['insurance_provider_id', 'appeal_level_id']])
			->addColumn('insurance_provider_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('appeal_level_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => false,
			])
			->addColumn('client_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
			])
			->addColumn('days_to_respond', 'integer', [
				'limit' => 10,
				'null' => true,
			])
			->addColumn('max_days', 'integer', [
				'limit' => 10,
				'null' => true,
			])
			->addColumn('agency_id', 'integer', [
				'limit' => 10,
				'null' => true,
				'default' => null
			])
			->addColumn('label', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->addForeignKey('client_id', 'clients', 'id')
			->addIndex([
				'appeal_level_id',
			])
			->addIndex([
				'insurance_provider_id',
			])
			->addIndex([
				'client_id'
			])
			->addIndex([
				'agency_id'
			])
			->create();
	}
}
