<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAuditIdentifierToAppeals extends AbstractMigration
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
		$this->table('appeals')
			->addColumn('audit_identifier', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true
			])
			->addIndex([
				'audit_identifier'
			])
			->save();
	}
}
