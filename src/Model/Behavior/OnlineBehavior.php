<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use InvalidArgumentException;

/**
 * Online behavior
 *
 * Behavior for finding / updating records timestamp of when it was last active
 * and returning records within a small window.
 */
class OnlineBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'fields' => [
			'primary_key' => 'UserId',
			'last_seen' => 'LastSeen',
		],
		'threshold' => '-5 minutes',
		'implementedFinders' => [
			'online' => 'findOnline',
		],
	];

	/**
	 * Find Online
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \App\Model\Behavior\Cake\ORM\Query
	 */
	public function findOnline(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->gte(
				$this->getConfig('fields.last_seen'),
				new FrozenTime($this->getConfig('threshold'))
			);
		});
	}

	/**
	 * Update Last Seen
	 *
	 * @param mixed $id
	 * @return bool
	 */
	public function updateLastSeen(mixed $id): bool
	{
		if (empty($id)) {
			throw new InvalidArgumentException(__(
				'A primary key must be provided to update last seen value.'
			));
		}

		$q = $this->table()->query()->update();

		$q->set([
			$this->getConfig('fields.last_seen') => new FrozenTime('now'),
		])->where([
			$this->getConfig('fields.primary_key') => $id,
		]);

		$q->execute();

		return true;
	}
}
