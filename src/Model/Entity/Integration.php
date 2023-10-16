<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Integration Entity.
 */
class Integration extends Entity
{
	/**
	 * @var string
	 */
	public const MIRTH_CONNECT = 'mirthConnect';

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
		'config_parsed'
	];

	/**
	 * Virtual 'config_parsed' property
	 * Parse the JSON stored config for this integration
	 *
	 * @return array
	 */
	protected function _getConfigParsed(): array
	{
		$json = $this->get('config_json') ?? "{}";

		return (array) json_decode($json) ?? [];
	}

	/**
	 * Get all valid values for the integration_name
	 * @return array
	 */
	public static function getAllNames(): array
	{
		return [
			self::MIRTH_CONNECT
		];
	}
}
