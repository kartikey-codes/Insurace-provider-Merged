<?php

declare(strict_types=1);

namespace App\Lib\TenantUtility;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Table;
use Cake\Routing\Router;

/**
 * Class for handling tenant
 *
 * @todo This class was created by Logic but uses static methods and is incompatible with \Cake\ORM\Locator\LocatorAwareTrait
 * So table fetching will need to be refactored.
 */
class TenantUtility
{
	/** @var string */
	public const ADMIN_TENANT_ID_HEADER = 'Admin-Client-Id';

	/** @var string */
	public const ADMIN_TENANT_QUERY_PARAMETER = 'Admin-Client-Id';

	/**
	 * Get Tenant ID from Request
	 *
	 * @return mixed
	 */
	public static function getTenantIdFromRequest()
	{
		$request = Router::getRequest();

		if ($request === null) {
			return null;
		}

		$identity = $request->getAttribute('identity');

		// Abort if can't extract identity from Authentication
		if ($identity === null) {
			return null;
		}

		// Get user entity object from identity
		$user = $identity->getOriginalData();

		// Allow admin passing tenant ID in query string for GET requests (preview file, etc.)
		$adminQueryStringTenantId = $request->getQuery(self::ADMIN_TENANT_QUERY_PARAMETER, null);

		$adminTenantIds = $request->getHeader(self::ADMIN_TENANT_ID_HEADER);
		$requestAdminTenantId = null;
		if (!empty($adminTenantIds)) {
			$requestAdminTenantId = intval($adminTenantIds[0]);
		} else if (!empty($adminQueryStringTenantId)) {
			$requestAdminTenantId = intval($adminQueryStringTenantId);
		} else {
			$requestAdminTenantId = $user->client_id;
		}

		// Use request admin tenant ID if available, fall back to user client id
		if ($user->isAdmin()) {
			return $requestAdminTenantId;
		} else {
			return $user->client_id;
		}

		// Fallback to null
		return null;
	}

	/**
	 * Is Admin
	 *
	 * @return bool
	 */
	public static function isAdmin(): bool
	{
		$request = Router::getRequest();
		if ($request === null) {
			return false;
		}

		$identity = $request->getAttribute('identity');
		if ($identity === null) {
			return false;
		}

		$user = $identity->getOriginalData();

		if ($user === null) {
			return false;
		} else {
			return $user->isAdmin();
		}
	}

	/**
	 * Is Vendor
	 *
	 * @return bool
	 */
	public static function isVendorUser(): bool
	{
		$request = Router::getRequest();
		if ($request === null) {
			return false;
		}

		$identity = $request->getAttribute('identity');
		if ($identity === null) {
			return false;
		}

		$user = $identity->getOriginalData();
		if (!$user->id) {
			return false;
		}

		/** @var \App\Model\Table\UsersTable */
		$users = FactoryLocator::get('Table')->get('Users');

		/** @var \App\Model\Entity\User */
		$user = $users->get($user->id, ['skipTenantCheck' => true]); // TODO why do we need to refresh user here?

		if ($user === null) {
			return false;
		} else {
			return $user->isVendorUser();
		}
	}

	/**
	 * Get Tenant ID from Entity
	 *
	 * @return mixed
	 */
	public static function getTenantIdFromEntity(Table $table, EntityInterface $entity)
	{
		$tenantForeignKeyName = Configure::read('Multitenancy.tenantForeignKeyName');
		$entityTenantId = $entity->{$tenantForeignKeyName};

		if ($entityTenantId) {
			return $entityTenantId;
		}

		// if entity doesn't have tenant id, it's possible this is a joint table, and client id is not fetched, try to fetch it from joint table
		if (get_class($entity) !== $table->getEntityClass()) {
			// get joint table primary keys from $entity
			$primaryKeys = is_array($table->getPrimaryKey()) ? $table->getPrimaryKey() : [$table->getPrimaryKey()];
			$primaryKeyValues = $entity->extract($primaryKeys);
			$actualEntity = $table->get($primaryKeyValues);
			$entityTenantId = $actualEntity->{$tenantForeignKeyName};
		}

		return $entityTenantId;
	}
}
