<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddConnectFieldsToCases extends AbstractMigration
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
		$this->table('cases')
			->removeIndex('meets_primary_umt_criteria')
			->removeIndex('meets_secondary_umt_criteria')
			->save();

		$this->table('cases')
			->removeColumn('meets_primary_umt_criteria')
			->removeColumn('meets_secondary_umt_criteria')
			->save();

		$this->table('appeals')
			->addColumn('appeal_status', 'string', [
				'default' => 'Open',
				'limit' => 50,
				'null' => false,
			])
			->addColumn('assigned_to_vendor_id', 'integer', [
				'default' => null,
				'limit' => 10,
				'null' => true,
				'signed' => false,
			])
			->addIndex([
				'appeal_status'
			])
			->addIndex([
				'assigned_to_vendor_id'
			])
			->save();
	}
}
