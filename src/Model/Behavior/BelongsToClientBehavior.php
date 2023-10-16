<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use BadMethodCallException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Belongs To Client behavior
 *
 * Handles finders for tables with entities created primarily by clients
 * or need to found by Client ID often.
 */
class BelongsToClientBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'foreignKey' => 'client_id',
		'implementedFinders' => [
			'byClientId' => 'findByClientId',
		],
	];

	/**
	 * Set up the behavior
	 *
	 * @param array<string, mixed> $config
	 * @return void
	 */
	public function initialize(array $config): void
	{
	}

	/**
	 * Find By Client ID
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByClientId(Query $query, array $options): Query
	{
		if (empty($options['id'])) {
			throw new BadMethodCallException(__('A client ID must be provided to find users.'));
		}

		return $query->where([
			$this->table()->aliasField($this->getConfig('foreignKey')) => $options['id'],
		]);
	}
}
