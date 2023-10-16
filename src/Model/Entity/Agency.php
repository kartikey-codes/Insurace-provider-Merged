<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AddressUtility\AddressUtility;
use Cake\ORM\Entity;

/**
 * Agency Entity.
 */
class Agency extends Entity
{
	// Outgoing Methods
	public const OUTGOING_METHOD_EMAIL = 'EMAIL';
	public const OUTGOING_METHOD_FAX = 'FAX';
	public const OUTGOING_METHOD_WEBSITE = 'WEBSITE';
	public const OUTGOING_METHOD_MAIL = 'MAIL';
	public const OUTGOING_METHOD_MANUAL = 'MANUAL';

	/**
	 * Labels for outgoing document delivery methods
	 *
	 * @var array
	 */
	public array $outgoingMethods = [
		self::OUTGOING_METHOD_EMAIL => 'Email',
		self::OUTGOING_METHOD_FAX => 'Fax',
		self::OUTGOING_METHOD_WEBSITE => 'Website',
		self::OUTGOING_METHOD_MAIL => 'Mail',
		self::OUTGOING_METHOD_MANUAL => 'Manual'
	];

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
		'outgoing_primary_method_label'
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

	/**
	 * Virtual 'outgoing_primary_method_label' property
	 * Displays the friendly display name of the primary outgoing method
	 *
	 * @return string
	 */
	protected function _getOutgoingPrimaryMethodLabel(): string
	{
		return $this->outgoingMethods[$this->outgoing_primary_method] ?? 'Unknown';
	}
}
