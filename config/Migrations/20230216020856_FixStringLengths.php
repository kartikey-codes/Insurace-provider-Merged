<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class FixStringLengths extends AbstractMigration
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
		$this->table('services')
			->changeColumn('description', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => true,
			])
			->save();

		$this->table('client_employees')
			->changeColumn('first_name', 'string', [
				'limit' => 50,
				'null' => false,
			])
			->changeColumn('last_name', 'string', [
				'limit' => 50,
				'null' => false,
			])
			->changeColumn('title', 'string', [
				'limit' => 50,
				'null' => true,
			])
			->changeColumn('state', 'string', [
				'limit' => 2,
				'null' => false,
			])
			->save();

		$this->table('audit_reviewers')
			->changeColumn('first_name', 'string', [
				'limit' => 50,
				'null' => false,
			])
			->changeColumn('last_name', 'string', [
				'limit' => 50,
				'null' => false,
			])
			->save();
	}
}
