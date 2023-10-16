<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\Behavior;

/**
 * InitClient behavior
 */
class InitForClientBehavior extends Behavior
{
	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [
		'initData' => [],
	];

	/**
	 * @var string
	 */
	protected string $tenantForeignKeyName;

	/**
	 * Set up the behavior
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);

		$this->tenantForeignKeyName = Configure::read('Multitenancy.tenantForeignKeyName');
	}

	/**
	 * Initialize data for a new client
	 *
	 * @param int $clientId
	 */
	public function initDataForClient(int $clientId): void
	{
		foreach ($this->getConfig('initData') as $initRecord) {
			$data = [$this->tenantForeignKeyName => $clientId] + $initRecord;
			$entity = $this->table()->newEntity($data, ['validate' => false]);
			$this->table()->saveOrFail($entity, ['skipTenantCheck' => true, 'checkRules' => false]);
		}
	}

	/**
	 * Clear client related data
	 *
	 * @param int $clientId
	 * @return void
	 * @throws \Cake\Datasource\Exception\MissingDatasourceConfigException
	 * @throws \Exception
	 */
	public function clearDataForClient(int $clientId): void
	{
		$this->table()->deleteAll([$this->tenantForeignKeyName => $clientId]); // this won't trigger beforeDelete event
	}
}
