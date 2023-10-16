<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Case Outcome Entity.
 */
class CaseOutcome extends Entity
{
	/**
	 * Favorable
	 *
	 * @var string
	 */
	public const STATUS_FAVORABLE = 'Favorable';

	/**
	 * Unfavorable
	 *
	 * @var string
	 */
	public const STATUS_UNFAVORABLE = 'Unfavorable';

	/**
	 * Withdrawn
	 *
	 * @var string
	 */
	public const STATUS_WITHDRAWN = 'Withdrawn';

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
	protected $_virtual = [];
}
