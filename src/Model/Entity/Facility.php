<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AddressUtility\AddressUtility;
use Cake\ORM\Entity;

/**
 * Facility Entity.
 */
class Facility extends Entity
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
		'full_address',
	];

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
