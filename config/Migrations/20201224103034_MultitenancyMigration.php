<?php

declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Core\Configure;

/**
 * Need to seed first:
 * bin/cake migrations seed --seed DefaultClientsSeed
 * Then:
 * bin/cake migrations migrate -vvv -t 20201220093123
 * bin/cake migrations migrate -vvv -t 20201224103034
 * Rollback:
 * bin/cake migrations rollback
 */
class MultitenancyMigration extends AbstractMigration
{
	public function up()
	{
		$tenantTableName = Configure::read('Multitenancy.tenantTableName');
		$userTalbeName = Configure::read('Multitenancy.userTableName');
		$foreignKeyName = Configure::read('Multitenancy.tenantForeignKeyName');

		$client = $this->fetchRow('select id from clients order by id;');
		if (!$client) {
			$this->getOutput()->writeln('clients table is empty, run DefaultClientsSeed first');
			return;
		}
		$client_id = $client['id'];
		$this->getOutput()->writeln('using default client_id ' . strval($client_id));

		foreach ($this->getTablesToAddTenantKey() as $entityTableName) {
			$table = $this->table($entityTableName);
			if (!$table->exists()) {
				$this->getOutput()->writeln('table ' . $entityTableName . ' does not exist, skip it.');
				continue;
			}
			$table->addColumn($foreignKeyName, 'integer', [
				'default' => $client_id,
				'null' => $entityTableName == $userTalbeName ? true : false,
			]);
			$table->update();

			$table->changeColumn($foreignKeyName, 'integer', [
				'default' => null,
				'null' => $entityTableName == $userTalbeName ? true : false,
			]);
			$table->addForeignKey($foreignKeyName, $tenantTableName, 'id');
			$table->update();

			$this->getOutput()->writeln('added ' . $foreignKeyName . ' to table ' . $entityTableName);
		}

		foreach (Configure::read('Multitenancy.tablesAlreadyHaveTenantKey') as $entityTableName) {
			$table = $this->table($entityTableName);
			$table->addForeignKey($foreignKeyName, $tenantTableName, 'id');
			$table->update();
		}

		// special handling for users table
		$this->execute("UPDATE {$userTalbeName} SET {$foreignKeyName}=null WHERE admin=1");
		$this->execute("UPDATE {$userTalbeName} SET {$foreignKeyName}={$client_id} WHERE admin=0");

		// special handling for tablesAlreadyHaveTenantKey, note this is not revertable
		foreach (Configure::read('Multitenancy.tablesAlreadyHaveTenantKey') as $entityTableName) {
			$this->execute("UPDATE {$entityTableName} SET {$foreignKeyName}={$client_id} WHERE {$foreignKeyName} is null");
		}
	}

	public function down()
	{
		$foreignKeyName = Configure::read('Multitenancy.tenantForeignKeyName');
		foreach ($this->getTablesToAddTenantKey() as $entityTableName) {
			$table = $this->table($entityTableName);
			if (!$table->exists()) {
				continue;
			}
			$table->dropForeignKey($foreignKeyName);
			$table->removeColumn($foreignKeyName);
			$table->update();
		}

		foreach (Configure::read('Multitenancy.tablesAlreadyHaveTenantKey') as $entityTableName) {
			$table = $this->table($entityTableName);
			$table->dropForeignKey($foreignKeyName);
			$table->update();
		}
	}

	private function getTablesToAddTenantKey()
	{
		return array_diff(
			Configure::read('Multitenancy.allTableNames'),
			[Configure::read('Multitenancy.tenantTableName')],
			Configure::read('Multitenancy.tablesAlreadyHaveTenantKey'),
			Configure::read('Multitenancy.tablesDontNeedTenantKey')
		);
	}
}
