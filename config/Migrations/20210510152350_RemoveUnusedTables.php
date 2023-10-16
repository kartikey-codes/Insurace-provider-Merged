<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveUnusedTables extends AbstractMigration
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
		// Alert Reasons
		$this->table('alert_reasons')->drop()->save();

		// Blurb Footnotes
		$this->table('blurb_footnotes')->drop()->save();

		// Calendar Events
		$this->table('calendar_events')->drop()->save();

		// Company Messages
		$this->table('company_messages')->drop()->save();

		// Messages
		$this->table('messages')->drop()->save();
	}
}
