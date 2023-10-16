<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\AddressUtility\AddressUtility;
use App\Lib\DateUtility\DateUtility;
use App\Lib\PeopleUtility\PeopleUtility;
use Cake\ORM\Entity;

/**
 * Patient Entity.
 */
class Patient extends Entity
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
		'full_name',
		'list_name',
		'document_name',
		'age',
		'is_birthday',
		'days_until_birthday',
		'full_address',
	];

	/**
	 * Virtual 'full_name' property
	 *
	 * @return string
	 */
	protected function _getFullName(): string
	{
		return PeopleUtility::combineFullName(
			$this->first_name,
			$this->middle_name,
			$this->last_name,
			null // No title
		);
	}

	/**
	 * Virtual 'list_name' property
	 *
	 * @return string
	 */
	protected function _getListName(): string
	{
		return PeopleUtility::combineListName(
			$this->first_name,
			$this->middle_name,
			$this->last_name,
			null // No title
		);
	}

	/**
	 * Virtual 'document_name' property
	 *
	 * @return string
	 */
	protected function _getDocumentName(): string
	{
		if (empty($this->get('secured'))) {
			return PeopleUtility::combineListName(
				$this->first_name,
				$this->middle_name,
				$this->last_name,
				null // No title
			);
		} else {
			return '(Secured Patient)';
		}
	}

	/**
	 * Virtual 'age' property
	 * Calculate the users age
	 *
	 * @return ?int
	 */
	protected function _getAge(): ?int
	{
		return DateUtility::getAgeInYears($this->date_of_birth);
	}

	/**
	 * Virtual 'is_birthday' property
	 * Is today the users birthday or not
	 *
	 * @return ?bool
	 */
	protected function _getIsBirthday(): ?bool
	{
		return DateUtility::getIsAnniversary($this->date_of_birth);
	}

	/**
	 * Virtual 'days_until_birthday' property
	 * Returns days until next birthday, or null if unprovided
	 *
	 * @return ?int
	 */
	protected function _getDaysUntilBirthday(): ?int
	{
		return DateUtility::getDaysUntilAnniversary($this->date_of_birth);
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
