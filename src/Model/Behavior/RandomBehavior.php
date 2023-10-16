<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;

/**
 * Random behavior
 *
 * Handles finding a random record, which helps with factories
 * and fake records for testing.
 */
class RandomBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'clientIdField' => 'client_id',
	];

	/**
	 * Get a random entity from all
	 *
	 * @return \Cake\ORM\EntityInterface
	 */
	public function getRandomFromAll(): EntityInterface
	{
		return $this->table()
			->find()
			->all()
			->sample(1)
			->first();
	}

	/**
	 * Get a random entity by client ID
	 *
	 * @param int $clientId
	 * @return \Cake\ORM\EntityInterface
	 */
	public function getRandomByClientId(int $clientId): EntityInterface
	{
		return $this->table()
			->find('all', [
				'skipTenantCheck' => true,
			])
			->where([
				$this->table()->aliasField($this->getConfig('clientIdField')) => $clientId,
			])
			->all()
			->sample(1)
			->first();
	}
}
