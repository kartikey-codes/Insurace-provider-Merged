<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\User;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Case Entity.
 *
 * Can't use Case as a PHP class name, so we're using CaseEntity
 */
class CaseEntity extends Entity
{
	/**
	 * Open with appeals
	 *
	 * @var string
	 */
	public const STATUS_OPEN = 'Open';

	/**
	 * Closed and no more action necessary
	 *
	 * @var string
	 */
	public const STATUS_CLOSED = 'Closed';

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
		'list_name',
		'status',
		'length_of_stay',
		'can_add_appeal',
	];

	/**
	 * Various statuses for this entity matching to their labels
	 *
	 * @var array
	 */
	protected array $statuses = [
		self::STATUS_OPEN => 'Open',
		self::STATUS_CLOSED => 'Closed',
	];

	/**
	 * Getter for total claim amount
	 * @param mixed $amount
	 * @return float
	 */
	protected function _getTotalClaimAmount($amount): float
	{
		if (empty($amount)) {
			return 0.00;
		}

		return round((float) $amount, 2);
	}

	/**
	 * Getter for disputed amount
	 * @param mixed $amount
	 * @return float
	 */
	protected function _getDisputedAmount($amount): float
	{
		if (empty($amount)) {
			return 0.00;
		}

		return round((float) $amount, 2);
	}

	/**
	 * Getter for reimbursement amount
	 * @param mixed $amount
	 * @return float
	 */
	protected function _getReimbursementAmount($amount): float
	{
		if (empty($amount)) {
			return 0.00;
		}

		return round((float) $amount, 2);
	}

	/**
	 * Getter for settled amount
	 * @param mixed $amount
	 * @return float
	 */
	protected function _getSettledAmount($amount): float
	{
		if (empty($amount)) {
			return 0.00;
		}

		return round((float) $amount, 2);
	}

	/**
	 * Virtual 'list_name' property
	 * Displays the ID and display name together for links and such.
	 *
	 * @return string
	 */
	protected function _getListName(): string
	{
		if (empty($this->id)) {
			return '';
		}

		// Start with the ID
		$listName = '[' . $this->id . ']';

		// Add the patient name
		if (!empty($this->patient)) {
			$listName .= ' ' . $this->patient->list_name;
		}

		return $listName;
	}

	/**
	 * Virtual 'status' property
	 * Get the case's status
	 *
	 * @return string
	 */
	protected function _getStatus(): string
	{
		if (!empty($this->closed)) {
			return self::STATUS_CLOSED;
		}

		return self::STATUS_OPEN;
	}

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

	/**
	 * Determine if an appeal can be added to this case
	 *
	 * @return bool
	 */
	protected function _getCanAddAppeal(): bool
	{
		if (!empty($this->closed)) {
			return false;
		}

		if (!empty($this->appeals)) {
			foreach ($this->appeals as $appeal) {
				if (!$appeal->is_finished) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Mark this entity as assigned to a different user
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setAssignedTo(User $user): self
	{
		$this->set('assigned', new FrozenTime());
		$this->set('assigned_to', $user->get('id'));

		return $this;
	}

	/**
	 * Mark this entity as unassigned from any user
	 *
	 * @return self
	 */
	public function clearAssignedTo(): self
	{
		$this->set('assigned', null);
		$this->set('assigned_to', null);

		return $this;
	}

	/**
	 * Mark this entity as closed by a user
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setClosedBy(User $user): self
	{
		$this->set('closed', new FrozenTime());
		$this->set('closed_by', $user->get('id'));
		$this->set('unable_to_complete', false);

		return $this;
	}

	/**
	 * Mark this entity as reopened by a user
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setOpenedBy(User $user): self
	{
		$this->set('closed', null);
		$this->set('closed_by', null);
		$this->set('unable_to_complete', false);

		return $this;
	}
}
