<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Active behavior
 *
 * Handles models that have an active/inactive flag
 */
class ActiveBehavior extends Behavior
{
	/**
	 * @var array<string, mixed>
	 */
	protected $_defaultConfig = [
		'field' => 'active',
	];

	/**
	 * Set up the behavior
	 *
	 * @return void
	 */
	public function initialize(array $config): void
	{
	}

	/**
	 * Find Active
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findActive(Query $query, array $options): Query
	{
		return $query->where(function ($exp) {
			return $exp->eq($this->table()->aliasField($this->getConfig('field')), true);
		});
	}

	/**
	 * Find Inactive
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findInactive(Query $query, array $options): Query
	{
		return $query->where(function ($exp) {
			return $exp->eq($this->table()->aliasField($this->getConfig('field')), false);
		});
	}
}
