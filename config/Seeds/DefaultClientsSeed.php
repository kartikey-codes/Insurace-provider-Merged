<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Default Clients seed.
 */
class DefaultClientsSeed extends AbstractSeed
{
	const ADAPTER_SQLSRV = 'sqlsrv';
	const ADAPTER_PGSQL = 'pgsql';

	const TABLE_CLIENTS = 'clients';

	/**
	 * Run Method
	 * @return void
	 */
	public function run(): void
	{
		$date = date('Y-m-d H:i:s');

		$data = [
			'name' => 'Default Client',
			'npi_number' => 12345,
			'created' => $date,
			'modified' => $date
		];

		$adapterType = $this->getAdapter()->getAdapterType();

		if ($adapterType == self::ADAPTER_SQLSRV) {
			$data['id'] = 1;
		} elseif ($adapterType == self::ADAPTER_PGSQL) {
			// Null
		} else {
			// Null
		}

		$table = $this->table(self::TABLE_CLIENTS);

		// @db_specific for sqlserver
		if ($adapterType == self::ADAPTER_SQLSRV) {
			$this->execute('SET IDENTITY_INSERT ' . self::TABLE_CLIENTS . ' ON');
		}

		$table->insert($data)->save();

		// @db_specific for sqlserver
		if ($adapterType == self::ADAPTER_SQLSRV) {
			$this->execute('SET IDENTITY_INSERT ' . self::TABLE_CLIENTS . ' OFF');
		}
	}
}
