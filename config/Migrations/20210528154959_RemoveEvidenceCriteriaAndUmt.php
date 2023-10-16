<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveEvidenceCriteriaAndUmt extends AbstractMigration
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
			->removeColumn('utilization_management_tool_id')
			->removeColumn('require_primary_umt_answers')
			->removeColumn('require_secondary_umt_answers')
			->save();

		$this->table('case_evidence_criteria')
			->drop()
			->save();

		$this->table('case_umt_answers')
			->drop()
			->save();

		$this->table('evidence_criteria')
			->drop()
			->save();

		$this->table('utilization_management_tools')
			->drop()
			->save();
	}
}
