<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Inflector;

/**
 * Permission Entity.
 */
class Permission extends Entity
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
		'controller_title',
	];

	/**
	 * Virtual 'controller_title' property
	 *
	 * @return string
	 */
	protected function _getControllerTitle(): string
	{
		// Custom
		switch ($this->controller) {
			case 'EvidenceCriteria':
				return 'Evidence Criteria';
		}

		// Fallback to inflector
		$tableized = Inflector::tableize($this->controller);

		return Inflector::humanize($tableized);
	}
}
