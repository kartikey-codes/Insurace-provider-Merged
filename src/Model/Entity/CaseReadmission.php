<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Case Readmission Entity.
 */
class CaseReadmission extends Entity
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
		'length_of_stay',
	];

	/**
	 * Virtual 'length_of_stay' property
	 *
	 * Get the total days between admit and discharge
	 *
	 * @return int|null
	 */
	protected function _getLengthOfStay(): ?int
	{
		if (empty($this->admit_date)) {
			return null;
		}

		if (empty($this->discharge_date)) {
			return null;
		}

		if (!is_object($this->admit_date) || !is_object($this->discharge_date)) {
			return null;
		}

		return $this->admit_date->diffInDays($this->discharge_date);
	}
}
