<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Lock Out behavior
 *
 * Handles locking and unlocking a model based on a timestamp
 */
class LockOutBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'primaryKey' => 'id',
		'lockField' => 'locked',
		'expirationField' => 'lock_expiration',
		'duration' => 30, // Minutes
		'maxAttempts' => 5,
	];

	/**
	 * Set up the behavior
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);
	}

	/**
	 * Get aliased locked boolean field
	 *
	 * @return string
	 */
	private function aliasLockField(): string
	{
		return $this->table()->aliasField($this->getConfig('lockField'));
	}

	/**
	 * Get aliased locked expiration timestamp field
	 *
	 * @return string
	 */
	private function aliasExpirationField(): string
	{
		return $this->table()->aliasField($this->getConfig('expirationField'));
	}

	/**
	 * Find Entities Not Locked Out
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findUnlocked(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasLockField(), false);
		});
	}

	/**
	 * Find Entities Locked Out
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findLocked(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasLockField(), true);
		});
	}

	/**
	 * Find Lock Expired Entities
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findLockExpired(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->and([
				$exp->eq($this->aliasLockField(), true),
				$exp->lte($this->aliasExpirationField(), new FrozenTime()),
			]);
		});
	}

	/**
	 * Check an entities lock-out status
	 *
	 * @param mixed id
	 * @return bool
	 */
	public function isEntityLocked(mixed $id): bool
	{
		$entity = $this->table()->get($id);
		$isLocked = (bool)$entity->get($this->getConfig('lockField'));

		return $isLocked;
	}

	/**
	 * Check when an entities lock-out status expires
	 *
	 * @param mixed id
	 * @return ?\Cake\I18n\FrozenTime
	 */
	public function getEntityLockExpiration(mixed $id): ?FrozenTime
	{
		$entity = $this->table()->get($id);
		$expiration = $entity->get($this->getConfig('expirationField'));

		return $expiration;
	}

	/**
	 * Clear an entities lock-out status
	 *
	 * @param mixed $id
	 * @return bool
	 */
	public function unlock(mixed $id): bool
	{
		$entity = $this->table()->get($id);

		$entity->set([
			$this->getConfig('lockField') => false,
			$this->getConfig('expirationField') => null,
		]);

		$this->table()->saveOrFail($entity, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		return true;
	}

	/**
	 * Lock an entity out for a time duration
	 *
	 * @param mixed $id
	 * @param ?\Cake\I18n\FrozenTime $expiration
	 * @return bool
	 */
	public function lockOut(mixed $id, ?FrozenTime $expiration = null): bool
	{
		if (empty($expiration)) {
			$expiration = new FrozenTime(
				'+' . $this->getConfig('duration') . ' minutes',
			);
		}

		$entity = $this->table()->get($id);

		$entity->set([
			$this->getConfig('lockField') => true,
			$this->getConfig('expirationField') => $expiration,
		]);

		$this->table()->saveOrFail($entity, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		return true;
	}

	/**
	 * Check failed logins and update lockout status if necessary
	 *
	 * @param mixed $id
	 * @return bool
	 */
	public function checkLockout(mixed $id): bool
	{
		// Calculate how far back we should care about failed logins
		$startTime = new FrozenTime(
			'-' . $this->getConfig('duration') . ' minutes',
		);

		if ($this->exceedsMaxFailedAttempts($id, $startTime)) {
			$this->lockOut($id);
		}

		return true;
	}

	/**
	 * Count number of failed logins since a specific time
	 *
	 * @param mixed $id
	 * @param \Cake\I18n\FrozenTime $startTime
	 * @return int
	 */
	public function countFailedAttempts(mixed $id, FrozenTime $startTime): int
	{
		return $this->table()->UserLogins
			->find('failed')
			->find('login')
			->where([
				'user_id' => $id,
				'created >=' => $startTime,
			])
			->order([
				'created' => 'desc',
			])
			->count();
	}

	/**
	 * Return whether the entity exceeded the failed logins since a specific time
	 *
	 * @param mixed $id
	 * @param \Cake\I18n\FrozenTime $startTime
	 * @return bool
	 */
	public function exceedsMaxFailedAttempts(mixed $id, FrozenTime $startTime): bool
	{
		/**
		 * @var int $maxAttempts
		 */
		$maxAttempts = $this->getConfig('maxAttempts');

		// Check how many times we failed to login within the threshold time
		$failedAttempts = $this->countFailedAttempts($id, $startTime);

		// Too many failed logins within the threshold
		return $failedAttempts >= $maxAttempts;
	}
}
