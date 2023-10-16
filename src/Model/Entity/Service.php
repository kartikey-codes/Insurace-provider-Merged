<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Service Entity
 *
 * @property int $id
 * @property int $client_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 * @property string|null $name
 * @property string|null $description
 * @property bool $active
 * @property bool $client_owned
 *
 * @property \App\Model\Entity\Client $client
 */
class Service extends Entity
{
	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array<string, bool>
	 */
	protected $_accessible = [
		'client_id' => true,
		'created' => true,
		'created_by' => true,
		'modified' => true,
		'modified_by' => true,
		'name' => true,
		'description' => true,
		'active' => true,
		'client_owned' => true,
		'client' => true,
		'facilities' => true,
	];
}
