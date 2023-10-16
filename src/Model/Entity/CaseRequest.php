<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\ORM\Entity;

/**
 * Case Request Entity.
 */
class CaseRequest extends Entity
{
	/**
	 * Open / new status
	 *
	 * @var string
	 */
	public const STATUS_OPEN = 'Open';

	/**
	 * Unable To Complete
	 *
	 * @var string
	 */
	public const STATUS_UTC = 'UTC';

	/**
	 * Completed and no more action necessary
	 *
	 * @var string
	 */
	public const STATUS_COMPLETED = 'Completed';

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
		'days_due_left',
		'is_overdue',
		'status_label',
		'type_label'
	];

	/**
	 * Virtual 'is_overdue' property
	 * Return if the due date is past
	 *
	 * @return ?bool
	 */
	protected function _getIsOverdue(): ?bool
	{
		if (empty($this->get('due_date'))) {
			return null;
		}

		$due = new FrozenDate($this->get('due_date'));

		return $due->isPast();
	}

	/**
	 * Virtual 'days_due_left' property
	 *
	 * Get the total days between due date and today
	 *
	 * @return int|null
	 */
	protected function _getDaysDueLeft(): ?int
	{
		if (empty($this->due_date)) {
			return null;
		}

		return $this->due_date->diffInDays();
	}

	/**
	 * Virtual 'status_label' property
	 *
	 * Get the value for the front-end for the status of this request
	 *
	 * @return string
	 */
	protected function _getStatusLabel(): string
	{
		if (!empty($this->unable_to_complete)) {
			return 'Unable To Complete';
		}

		if (!empty($this->completed)) {
			return 'Completed';
		}

		return 'Open';
	}

	/**
	 * Virtual 'type_label' property
	 *
	 * Get the value for the front-end for the case reuqest type of this request
	 *
	 * @return string
	 */
	protected function _getTypeLabel(): string
	{
		switch ($this->request_type) {
			case 'DOCUMENTATION':
				return 'Documentation';
			case 'HEARING':
				return 'Hearing';
			default:
				return 'Unknown';
		}
	}
}
