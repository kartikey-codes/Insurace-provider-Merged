<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * EnhancedFinder behavior
 *
 * Behavior providing additional methods for retrieving data.
 */
class EnhancedFinderBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'fullContain' => [],
		'limitedFields' => [
			'id',
			'name',
		],
	];

	/**
	 * Constructor hook method.
	 *
	 * Implement this method to avoid having to overwrite
	 * the constructor and call parent.
	 *
	 * @param array<string, mixed> $config The configuration settings provided to this behavior.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		// Overwrite limited finder fields
		if (!empty($config['limitedFields'])) {
			$this->setConfig('limitedFields', $config['limitedFields'], false);
		}
	}

	/**
	 * Finder for scarce entity data, such as id, name, active.
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findLimited(Query $query, array $options): Query
	{
		return $query->select($this->getConfig('limitedFields'));
	}

	/**
	 * Retrieve a single field value
	 *
	 * @param string $fieldName The name of the table field to retrieve.
	 * @param array $conditions An array of conditions for the find.
	 * @return mixed The value of the specified field from the first row of the result set.
	 */
	public function field(string $fieldName, array $conditions): mixed
	{
		$field = $this->table()->getAlias() . '.' . $fieldName;
		$query = $this->table()->find()->select($field)->where($conditions);
		$results = $query->all();

		if ($results->isEmpty()) {
			return null;
		}

		return $results->first()->{$fieldName};
	}

	/**
	 * Get full record with all associations we need
	 * Mostly for the view screen
	 *
	 * Passing 'skipTenantCheck' options here
	 * seems to cause PHP dev server to crash.
	 *
	 * @param mixed $id
	 * @param array $options
	 * @return \Cake\ORM\EntityInterface
	 */
	public function getFull(mixed $id, array $options = []): EntityInterface
	{
		return $this->table()->get($id, [
			'contain' => $this->getConfig('fullContain'),
		] + $options);
	}

	/**
	 * Finder for full list of associations
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findFull(Query $query, array $options): Query
	{
		return $query->contain($this->getConfig('fullContain'));
	}
}
