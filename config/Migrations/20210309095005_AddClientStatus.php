<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddClientStatus extends AbstractMigration
{
	public function up()
	{
		$table = $this->table('clients');
		$table->addColumn('status', 'string', [
			'default' => 'Active',
			'limit' => 50,
			'null' => false,
		])
			->addIndex(['status'])
			->update();
	}

	public function down()
	{
		$table = $this->table('clients');
		$table->removeIndex(['status'])
			->update();
		$table->removeColumn('status')
			->update();
	}
}
