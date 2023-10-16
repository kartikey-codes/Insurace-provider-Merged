<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Orderable behavior
 *
 * Handles default ordering for a model
 */
class OrderableBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'default' => [],
	];

	/**
	 * Set up the behavior
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);
	}

	/**
	 * Finder for general ordering
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \App\Model\Behavior\Cake\ORM\Query
	 */
	public function findOrdered(Query $query, array $options): Query
	{
		return $query->order($this->getConfig('default'));
	}

	/**
	 * Find ordered list
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \App\Model\Behavior\Cake\ORM\Query
	 */
	public function findOrderedList(Query $query, array $options): Query
	{
		return $query->find('ordered')->find('list', $options);
	}
}
