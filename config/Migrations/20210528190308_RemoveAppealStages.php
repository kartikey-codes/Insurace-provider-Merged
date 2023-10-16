<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveAppealStages extends AbstractMigration
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
		$this->table('appeals')
			->removeIndex('initial_reviewed_by')
			->removeIndex('determination_by')
			->removeIndex('final_reviewed_by')
			->save();

		$this->table('appeals')
			->removeColumn('appeal_template_id')
			->removeColumn('letter_footnotes')
			->removeColumn('initial_reviewed_first')
			->removeColumn('initial_reviewed_first_by')
			->removeColumn('initial_reviewed')
			->removeColumn('initial_reviewed_by')
			->removeColumn('determination_first')
			->removeColumn('determination_first_by')
			->removeColumn('determination')
			->removeColumn('determination_by')
			->removeColumn('final_reviewed_first')
			->removeColumn('final_reviewed_first_by')
			->removeColumn('final_reviewed')
			->removeColumn('final_reviewed_by')
			->save();
	}
}
