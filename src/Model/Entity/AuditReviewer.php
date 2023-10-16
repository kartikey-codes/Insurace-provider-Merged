<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\PeopleUtility\PeopleUtility;
use Cake\ORM\Entity;

/**
 * Audit Reviewer Entity.
 */
class AuditReviewer extends Entity
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
}
