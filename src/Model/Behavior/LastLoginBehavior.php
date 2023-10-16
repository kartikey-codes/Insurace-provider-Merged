<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Core\Exception\CakeException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;

/**
 * Last Login behavior
 *
 * Handles updating a last login timestamp and IP address field
 */
class LastLoginBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'primaryKey' => 'UserId',
		'timestampField' => 'LastLoginDate',
		'ipField' => 'LastLoginIp',
	];

	/**
	 * Set up the behavior
	 */
	public function initialize(array $config): void
	{
	}

	/**
	 * Update Last Login
	 *
	 * @param mixed $id
	 * @return bool
	 */
	public function updateLastLogin(mixed $id): bool
	{
		if (empty($id)) {
			throw new CakeException(__('A user ID must be provided to update last login.'));
		}

		$q = $this->table()
			->query()
			->update();

		$q->set([
			$this->getConfig('timestampField') => new FrozenTime('now'),
			$this->getConfig('ipField') => $_SERVER['REMOTE_ADDR'],
		])->where([
			$this->getConfig('primaryKey') => $id,
		]);

		$q->execute();

		return true;
	}
}
