<?php

declare(strict_types=1);

namespace App\Cache\Engine;

use App\Lib\TenantUtility\TenantUtility;
use Cake\Cache\CacheEngine;
use Cake\Cache\CacheRegistry;
use Cake\Cache\Engine\NullEngine;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Tenant Aware Cache Engine
 *
 * @todo Only use tenant aware cache for tables with client_id / tenant_id
 */
class TenantAwareEngine extends CacheEngine
{
	use LocatorAwareTrait;

	/**
	 * Instance of CacheEngine class
	 *
	 * @var \Cake\Cache\CacheEngine|null
	 */
	protected $_engine;

	/**
	 * @inheritDoc
	 */
	public function init(array $config = []): bool
	{
		if ($config['engine'] === null) {
			Log::warning("TenantAwareEngine 'engine' configuration is missing, using NullEngine instead.");
			$config['engine'] = NullEngine::class;
		}

		$registry = new CacheRegistry();
		$config['className'] = $config['engine'];
		unset($config['engine']);
		$this->_engine = $registry->load('tenantAwareEngine', $config);
		$registry->reset();
		unset($registry);

		//Log::info("TenantAwareEngine loaded '{$config['className']}'");
		return true;
	}

	/**
	 * Get tenant ID from request
	 *
	 * @param mixed $key
	 * @return string
	 */
	protected function getTenantKey($key): string
	{
		$tenantId = TenantUtility::getTenantIdFromRequest();
		if (empty($tenantId)) {
			return $key;
		} else {
			return "client-{$tenantId}-{$key}";
		}
	}

	/**
	 * Strip tenant key from string
	 *
	 * @param mixed $tenantKey
	 * @return string
	 */
	protected function stripTenantKey($tenantKey): string
	{
		$tenantId = TenantUtility::getTenantIdFromRequest();
		if (empty($tenantId)) {
			return $tenantKey;
		} else {
			$prefix = "client-{$tenantId}-";

			return substr($tenantKey, strlen($prefix));
		}
	}

	/**
	 * Get all tenant keys
	 *
	 * @param mixed $key
	 * @return array
	 */
	protected function getAllTenantKeys($key): array
	{
		$clients = $this->fetchTable('Clients');

		$clientIds = $clients
			->find('all', ['skipTenantCheck' => true])
			->find('withTrashed')
			->find('ordered')
			->all()
			->map(function ($client, $key) {
				return $client->id;
			});
		$allTenantKeys = [$key];
		foreach ($clientIds as $clientId) {
			$allTenantKeys[] = "client-{$clientId}-{$key}";
		}

		return $allTenantKeys;
	}

	/**
	 * @inheritDoc
	 */
	public function set($key, $value, $ttl = null): bool
	{
		//Log::debug("TenantAwareEngine set '{$key}'");
		return $this->_engine->set($this->getTenantKey($key), $value, $ttl);
	}

	/**
	 * @inheritDoc
	 */
	public function setMultiple($values, $ttl = null): bool
	{
		//Log::debug("TenantAwareEngine setMultiple");
		$tenantValues = [];
		foreach ($values as $key => $value) {
			$tenantValues[$this->getTenantKey($key)] = $value;
		}

		return $this->_engine->setMultiple($tenantValues, $ttl);
	}

	/**
	 * @inheritDoc
	 */
	public function get($key, $default = null)
	{
		//Log::debug("TenantAwareEngine get '{$key}'");
		return $this->_engine->get($this->getTenantKey($key), $default);
	}

	/**
	 * @inheritDoc
	 */
	public function getMultiple($keys, $default = null): iterable
	{
		//Log::debug("TenantAwareEngine getMultiple");
		$tenantKeys = [];
		foreach ($keys as $key) {
			$tenantKeys[] = $this->getTenantKey($key);
		}

		$tenantValues = $this->_engine->getMultiple($keys, $default);
		$values = [];
		foreach ($tenantValues as $tenantKey => $value) {
			$values[$this->stripTenantKey($tenantKey)] = $value;
		}

		return $values;
	}

	/**
	 * @inheritDoc
	 */
	public function increment(string $key, int $offset = 1)
	{
		return $this->_engine->increment($this->getTenantKey($key), $offset);
	}

	/**
	 * @inheritDoc
	 */
	public function decrement(string $key, int $offset = 1)
	{
		return $this->_engine->decrement($this->getTenantKey($key), $offset);
	}

	/**
	 * @inheritDoc
	 */
	public function delete($key): bool
	{
		//Log::debug("TenantAwareEngine delete {$key}");
		$result = false;
		// cannot use deleteMultiple here, since it will return after a single failure
		// we need to try to delete all keys regardless of failure
		foreach ($this->getAllTenantKeys($key) as $tenantKey) {
			if ($this->_engine->delete($tenantKey)) {
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function deleteMultiple($keys): bool
	{
		//Log::debug("TenantAwareEngine deleteMultiple");
		$result = false;
		foreach ($keys as $key) {
			foreach ($this->getAllTenantKeys($key) as $tenantKey) {
				if ($this->_engine->delete($tenantKey)) {
					$result = true;
				}
			}
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function clear(): bool
	{
		//Log::debug("TenantAwareEngine clear");
		return $this->_engine->clear();
	}

	/**
	 * @inheritDoc
	 */
	public function clearGroup(string $group): bool
	{
		//Log::debug("TenantAwareEngine clearGroup {$group}");
		return $this->_engine->clearGroup($group);
	}
}
