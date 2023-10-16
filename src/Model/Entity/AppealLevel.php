<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Appeal Level Entity.
 */
class AppealLevel extends Entity
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
		'display_name',
	];

	/**
	 * Virtual 'display_name' property
	 * Returns short name + name for lists
	 *
	 * @return string
	 */
	protected function _getDisplayName(): string
	{
		return sprintf(
			'[%s] %s',
			$this->get('short_name'),
			$this->get('name')
		);
	}
}
