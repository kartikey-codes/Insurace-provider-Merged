<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CleanUpAppeals extends AbstractMigration
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
		$this->table('appeal_levels')
			->removeIndex('allow_hearing_date')
			->save();

		$this->table('appeal_levels')
			->removeColumn('allow_hearing_date')
			->addColumn('order_number', 'integer', [
				'default' => 0,
				'null' => true
			])
			->save();

		// $this->execute("WITH al AS (SELECT [order_number], ROW_NUMBER() OVER (ORDER BY [id] ASC) AS rowNumber FROM appeal_levels) UPDATE al SET order_number = rowNumber;");
		// FIXTHIS $this->execute("WITH al AS (SELECT order_number, ROW_NUMBER() OVER (ORDER BY id ASC) AS rowNumber FROM appeal_levels) UPDATE al SET order_number = rowNumber;");
		// $this->execute("WITH al AS (SELECT order_number, ROW_NUMBER() OVER (ORDER BY id ASC) AS rowNumber FROM appeal_levels) UPDATE al SET order_number = rowNumber;");

		$this->table('appeal_notes')
			->removeColumn('subject')
			->save();

		$this->table('appeals')
			->removeColumn('current_status_due_date')
			->save();
	}
}
