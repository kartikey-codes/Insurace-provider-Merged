<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CleanUpAgain extends AbstractMigration
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
		$facilities = $this->table('facilities');
		$cases = $this->table('cases');

		if ($facilities->hasColumn('parent_company')) {
			$facilities->removeColumn('parent_company')
				->save();
		}

		if ($facilities->hasIndex('concurrent_faxes')) {
			$facilities->removeIndex('concurrent_faxes')
				->save();
		}

		if ($facilities->hasColumn('concurrent_faxes')) {
			$facilities->removeColumn('concurrent_faxes')
				->save();
		}

		if ($facilities->hasIndex('concurrent_emails')) {
			$facilities->removeIndex('concurrent_emails')
				->save();
		}

		if ($facilities->hasColumn('concurrent_emails')) {
			$facilities->removeColumn('concurrent_emails')
				->save();
		}

		if ($cases->hasIndex('concurrent_id')) {
			$cases->removeIndex('concurrent_id')
				->save();
		}

		if ($cases->hasColumn('concurrent_id')) {
			$cases->removeColumn('concurrent_id')
				->save();
		}

		$this->table('insurance_providers')
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->save();

		$this->table('patients')
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->save();
	}
}
