<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity.
 */
class Role extends Entity
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
		'member_names',
	];

	/**
	 * Virtual 'member_names' property
	 *
	 * @return string
	 */
	protected function _getMemberNames(): string
	{
		if (empty($this->users)) {
			return '';
		}

		return implode(', ', array_map(function ($user) {
			return $user->full_name;
		}, $this->users));
	}
}
