<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AddressUtility\AddressUtility;
use Cake\ORM\Entity;

/**
 * Agency Outgoing Profile Entity.
 */
class AgencyOutgoingProfile extends Entity
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
		'full_mail_to_address',
	];

	/**
	 * Virtual 'full_mail_to_address' property
	 * Displays the address concatenated
	 *
	 * @return string
	 */
	protected function _getFullMailToAddress(): string
	{
		return AddressUtility::combineToString(
			$this->mail_to_address_1,
			$this->mail_to_address_2,
			$this->city,
			$this->mail_to_state,
			$this->mail_to_zip
		);
	}
}
