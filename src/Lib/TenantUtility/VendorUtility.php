<?php

declare(strict_types=1);

namespace App\Lib\TenantUtility;

use Cake\Datasource\FactoryLocator;
use Cake\Log\Log;
use Cake\Routing\Router;

/**
 * Class for handling vendor
 *
 * @todo This class was created by Logic but uses static methods and is incompatible with \Cake\ORM\Locator\LocatorAwareTrait
 * So table fetching will need to be refactored.
 */
class VendorUtility
{
	/** @var string */
	public const ADMIN_TENANT_ID_HEADER = 'Admin-Vendor-Id';

	/**
	 * Return if user is a vendor user
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public static function isVendorUser(): bool
	{
		$user = VendorUtility::getUserFromRequest();

		if ($user === null) {
			return false;
		} else {
			return $user->isVendorUser();
		}
	}

	/**
	 * Get vendor ID from request
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public static function getVendorIdFromRequest()
	{
		$request = Router::getRequest();

		$user = VendorUtility::getUserFromRequest();

		if ($user === null) {
			return null;
		}

		$adminVendorIds = $request->getHeader(self::ADMIN_TENANT_ID_HEADER);
		$requestAdminVendorId = null;

		if (!empty($adminVendorIds)) {
			$requestAdminVendorId = $adminVendorIds[0];
		} else {
			$requestAdminVendorId = $user->vendor_id;
		}

		// Use request admin tenant ID if available, fall back to user vendor id
		if ($user->isAdmin()) {
			return $requestAdminVendorId;
		} else {
			return $user->vendor_id;
		}

		// Fallback to null
		return null;
	}

	/**
	 * Get user from request
	 *
	 * @return null|\App\Model\Entity\User
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public static function getUserFromRequest()
	{
		$request = Router::getRequest();
		if ($request === null) {
			return null;
		}

		$identity = $request->getAttribute('identity');
		if ($identity === null) {
			return null;
		}

		$user = $identity->getOriginalData();
		if (!$user->id) {
			return null;
		}

		/** @var \App\Model\Table\UsersTable */
		$users = FactoryLocator::get('Table')->get('Users');

		/** @var \App\Model\Entity\User */
		$user = $users->get($user->id, ['skipTenantCheck' => true, 'skipVendorCheck' => true]); // TODO why do we need to refresh user here?

		return $user;
	}

	/**
	 * @param mixed $caseId
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public static function isCaseAccessibleByVendorUser($caseId): bool
	{
		if (empty($caseId)) {
			Log::warning('VendorUtility: case not accessible since caseId is empty');

			return false;
		}

		/** @var int */
		$vendorId = VendorUtility::getVendorIdFromRequest();

		if ($vendorId === null) {
			Log::warning("VendorUtility: case not accessible since there's no vendorId");

			return false;
		}

		/** @var \App\Model\Table\CasesTable */
		$cases = FactoryLocator::get('Table')->get('Cases');
		/** @var \App\Model\Table\AppealsTable */
		$appeals = FactoryLocator::get('Table')->get('Appeals');
		$caseEntity = $cases->getFull($caseId);

		if (empty($caseEntity)) {
			Log::warning('VendorUtility: case not accessible since caseId is not found');

			return false;
		}

		if (empty($caseEntity->appeals)) {
			Log::warning("VendorUtility: case not accessible since there's no appeals");

			return false;
		}

		foreach ($caseEntity->appeals as $appeal) {
			$appealEntity = $appeals->getFull($appeal->id);

			if ($appealEntity->assigned_to_vendor_id == $vendorId) {
				return true;
			}
		}

		Log::warning('VendorUtility: case not accessible since no appeal has assigned_to_vendor_id ' . strval($vendorId));

		return false;
	}

	/**
	 * @param mixed $appealId
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public static function isAppealAccessibleByVendorUser($appealId): bool
	{
		if (empty($appealId)) {
			Log::warning('VendorUtility: appeal not accessible since appealId is empty');

			return false;
		}

		/** @var int */
		$vendorId = VendorUtility::getVendorIdFromRequest();

		if ($vendorId === null) {
			Log::warning("VendorUtility: appeal not accessible since there's no vendorId");

			return false;
		}

		/** @var \App\Model\Table\AppealsTable */
		$appeals = FactoryLocator::get('Table')->get('Appeals');
		$appealEntity = $appeals->getFull($appealId);

		if (empty($appealEntity)) {
			Log::warning('VendorUtility: appeal not accessible since appealId is not found');

			return false;
		}

		if ($appealEntity->assigned_to_vendor_id == $vendorId) {
			return true;
		}

		Log::warning('VendorUtility: appeal not accessible since assigned_to_vendor_id does not equal ' . strval($vendorId));

		return false;
	}
}
