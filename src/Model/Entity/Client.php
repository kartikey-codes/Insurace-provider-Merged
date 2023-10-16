<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AddressUtility\AddressUtility;
use App\Lib\DateUtility\DateUtility;
use App\Lib\PeopleUtility\PeopleUtility;
use Cake\ORM\Entity;

/**
 * Client Entity.
 */
class Client extends Entity
{
	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * @var array
	 */
	protected $_accessible = [
		'id' => false,
		'*' => true,
	];

	/**
	 * Exposed virtual fields
	 *
	 * @var array
	 */
	protected $_virtual = [
		'contact_full_name',
		'full_address',
	];

	/**
	 * Is this client subscribed or not
	 *
	 * @return bool
	 */
	public function isSubscribed(): bool
	{
		return !empty($this->subscription_active);
	}

	/**
	 * Virtual 'contact_full_name' property
	 *
	 * @return string
	 */
	protected function _getContactFullName(): string
	{
		return PeopleUtility::combineFullName(
			$this->contact_first_name,
			null, // No middle name
			$this->contact_last_name,
			$this->contact_title
		);
	}

	/**
	 * Virtual 'full_address' property
	 * Displays the address concatenated
	 *
	 * @return string
	 */
	protected function _getFullAddress(): string
	{
		return AddressUtility::combineToString(
			$this->street_address_1,
			$this->street_address_2,
			$this->city,
			$this->state,
			$this->zip
		);
	}
}
