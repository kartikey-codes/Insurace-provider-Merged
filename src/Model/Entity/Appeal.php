<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Appeal Entity.
 */
class Appeal extends Entity
{
	/**
	 * Created by Client
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
	 * Submitted for Vendor assignment
	 *
	 * @var string
	 */
	public const STATUS_SUBMITTED = 'Submitted';

	/**
	 * Assigned to a Vendor for completion
	 *
	 * @var string
	 */
	public const STATUS_ASSIGNED = 'Assigned';

	/**
	 * Vendor completed appeal for Client
	 *
	 * @var string
	 */
	public const STATUS_COMPLETED = 'Completed';

	/**
	 * Vendor returned to Client
	 *
	 * @var string
	 */
	public const STATUS_RETURNED = 'Returned';

	/**
	 * Client cancelled appeal
	 *
	 * @var string */
	public const STATUS_CANCELLED = 'Cancelled';

	/**
	 * Client is finished with appeal
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
		'is_overdue',
		'is_finished',
		'can_cancel',
		'can_close',
		'can_delete',
		'can_reopen',
		'can_submit',
		'pdf_title',
		'pdf_url',
		'cover_page_url',
		'status_icon',
		'status_variant'
	];

	/**
	 * Return the available statuses for this entity
	 *
	 * @return array
	 */
	public function getStatuses(): array
	{
		return [
			self::STATUS_OPEN,
			self::STATUS_UTC,
			self::STATUS_SUBMITTED,
			self::STATUS_ASSIGNED,
			self::STATUS_COMPLETED,
			self::STATUS_RETURNED,
			self::STATUS_CANCELLED,
			self::STATUS_CLOSED,
		];
	}

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
	 * Virtual 'is_finished' property
	 * Return if the status is considered done and over with
	 *
	 * @return bool
	 */
	protected function _getIsFinished(): bool
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_OPEN:
			case self::STATUS_UTC:
			case self::STATUS_SUBMITTED:
			case self::STATUS_ASSIGNED:
			case self::STATUS_COMPLETED:
			case self::STATUS_RETURNED:
				return false; // Something can be done
			case self::STATUS_CANCELLED:
			case self::STATUS_CLOSED:
				return true; // Client no longer concerned with appeal (finished)
			default:
				return false;
		}
	}

	/**
	 * Virtual 'status_icon' property
	 * Return icon representing status
	 *
	 * @return string
	 */
	protected function _getStatusIcon(): string
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_OPEN:
				return 'envelope-open';
				break;
			case self::STATUS_UTC:
				return 'exclamation-triangle';
				break;
			case self::STATUS_SUBMITTED:
				return 'users';
				break;
			case self::STATUS_ASSIGNED:
				return 'user';
				break;
			case self::STATUS_COMPLETED:
				return 'check-circle';
				break;
			case self::STATUS_RETURNED:
				return 'exclamation-triangle';
				break;
			case self::STATUS_CANCELLED:
				return 'ban';
				break;
			case self::STATUS_CLOSED:
				return 'check';
				break;
			default:
				return 'info-circle';
		}
	}

	/**
	 * Virtual 'status_variant' property
	 * Return color variant representing status
	 *
	 * @return string
	 */
	protected function _getStatusVariant(): string
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_OPEN:
				return "light";
				break;
			case self::STATUS_SUBMITTED:
				return "info";
				break;
			case self::STATUS_ASSIGNED:
				return "primary";
				break;
			case self::STATUS_COMPLETED:
				return "success";
				break;
			case self::STATUS_RETURNED:
				return "warning";
				break;
			case self::STATUS_CANCELLED:
				return "danger";
				break;
			case self::STATUS_CLOSED:
				return "success";
				break;
			default:
				return "dark";
				break;
		}
	}

	/**
	 * Virtual 'can_cancel' property
	 * Return if entity is eligible for cancelling
	 *
	 * @return bool
	 */
	protected function _getCanCancel(): bool
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_OPEN:
			case self::STATUS_UTC;
			case self::STATUS_SUBMITTED:
			case self::STATUS_ASSIGNED:
			case self::STATUS_COMPLETED:
			case self::STATUS_RETURNED:
				return true; // Can be cancelled
			case self::STATUS_CANCELLED:
			case self::STATUS_CLOSED:
				return false; // Client closed or already cancelled
			default:
				return false;
		}
	}

	/**
	 * Virtual 'can_close' property
	 * Return if entity is eligible for closing
	 *
	 * @return bool
	 */
	protected function _getCanClose(): bool
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_COMPLETED:
			case self::STATUS_RETURNED:
				return true;
			default:
				return false;
		}
	}

	/**
	 * Virtual 'can_delete' property
	 * Return if entity is eligible for deletion
	 *
	 * @return bool
	 */
	protected function _getCanDelete(): bool
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_UTC:
			case self::STATUS_OPEN:
			case self::STATUS_CANCELLED:
				return true;
			default:
				return false;
		}
	}

	/**
	 * Virtual 'can_reopen' property
	 * Return if entity is eligible for reopening
	 *
	 * @return bool
	 */
	protected function _getCanReopen(): bool
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_SUBMITTED:
			case self::STATUS_ASSIGNED:
			case self::STATUS_CLOSED:
			case self::STATUS_CANCELLED:
				return true;
			default:
				return false;
		}
	}

	/**
	 * Virtual 'can_submit' property
	 * Return if entity is eligible for submitting to vendor service
	 *
	 * @return bool
	 */
	protected function _getCanSubmit(): bool
	{
		switch ($this->get('appeal_status')) {
			case self::STATUS_OPEN:
			case self::STATUS_RETURNED:
				return true;
			default:
				return false;
		}
	}

	/**
	 * Virtual 'pdf_title' property
	 * Returns a title for the PDF document generated
	 *
	 * @return ?string
	 */
	protected function _getPdfTitle(): ?string
	{
		$title = $this->patient?->list_name ?: `Response {$this->id}`;

		return $title;
	}

	/**
	 * Virtual 'pdf_url' property
	 * Returns full URL to view appeal response PDF
	 *
	 * @return ?string
	 */
	protected function _getPdfUrl(): ?string
	{
		return Router::url([
			'prefix' => 'Client/Api',
			'controller' => 'Appeals',
			'action' => 'pdf',
			$this->id,
		], true);
	}

	/**
	 * Virtual 'cover_page_url' property
	 * Returns full URL to view appeal response PDF
	 *
	 * @return ?string
	 */
	protected function _getCoverPageUrl(): ?string
	{
		return Router::url([
			'prefix' => 'Client/Api',
			'controller' => 'Appeals',
			'action' => 'coverPage',
			$this->id,
		], true);
	}

	/**
	 * Mark this entity as assigned to a different user (client side)
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setAssignedTo(User $user): self
	{
		$this->set('assigned', new FrozenTime());
		$this->set('assigned_to', $user->id);

		return $this;
	}

	/**
	 * Mark this entity as completed
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setCompletedBy(User $user): self
	{
		$this->set('appeal_status', self::STATUS_COMPLETED);
		$this->set('unable_to_complete', false);
		$this->set('completed', new FrozenTime());
		$this->set('completed_by', $user->id);

		return $this;
	}

	/**
	 * Mark this entity as returned (unable to complete)
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setReturnedBy(User $user): self
	{
		$this->set('appeal_status', self::STATUS_RETURNED);
		$this->set('completed', null);
		$this->set('completed_by', null);

		return $this;
	}

	/**
	 * Mark this entity as reopened (reset status)
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setReopenedBy(User $user): self
	{
		$this->set('appeal_status', self::STATUS_OPEN);
		$this->set('unable_to_complete', false);

		$this->set('closed', null);
		$this->set('closed_by', null);

		$this->set('completed', null);
		$this->set('completed_by', null);

		$this->set('cancelled', null);
		$this->set('cancelled_by', null);

		return $this;
	}

	/**
	 * Mark this entity as submitted
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setSubmittedBy(User $user): self
	{
		$this->set('appeal_status', self::STATUS_SUBMITTED);
		$this->set('submitted', new FrozenTime());
		$this->set('submitted_by', $user->id);

		return $this;
	}

	/**
	 * Mark this entity as cancelled
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setCancelledBy(User $user): self
	{
		$this->set('appeal_status', self::STATUS_CANCELLED);
		$this->set('cancelled', new FrozenTime());
		$this->set('cancelled_by', $user->id);

		return $this;
	}

	/**
	 * Mark this entity as closed
	 *
	 * @param \App\Model\Entity\User $user
	 * @return self
	 */
	public function setClosedBy(User $user): self
	{
		$this->set('appeal_status', self::STATUS_CLOSED);
		$this->set('unable_to_complete', false);
		$this->set('closed', new FrozenTime());
		$this->set('closed_by', $user->id);

		return $this;
	}

	/**
	 * Assign entity to a vendor
	 *
	 * @param \App\Model\Entity\Vendor $vendor
	 * @return self
	 */
	public function assignToVendor(Vendor $vendor): self
	{
		$this->set([
			'assigned_to_vendor_id' => $vendor->id,
			'appeal_status' => self::STATUS_ASSIGNED,
		]);

		return $this;
	}

	/**
	 * Clear assigned vendor from an appeal
	 *
	 * @return self
	 */
	public function revertToSubmitted(): self
	{
		$this->set([
			'assigned_to_vendor_id' => null,
			'appeal_status' => self::STATUS_SUBMITTED,
		]);

		return $this;
	}

	/**
	 * Clear an appeal back to open status
	 *
	 * @return self
	 */
	public function revertToOpen(): self
	{
		$this->set([
			'assigned_to_vendor_id' => null,
			'appeal_status' => self::STATUS_OPEN,
			'unable_to_complete' => false,
			'cancelled' => null,
			'cancelled_by' => null,
			'submitted' => null,
			'submitted_by' => null,
			'completed' => null,
			'completed_by' => null,
		]);

		return $this;
	}

	/**
	 * Assign entity to a vendor by ID
	 *
	 * @param int $vendorId
	 * @return self
	 */
	public function assignToVendorById(int $vendorId): self
	{
		$this->set([
			'assigned_to_vendor_id' => $vendorId,
			'appeal_status' => self::STATUS_ASSIGNED,
			'unable_to_complete' => false
		]);

		return $this;
	}
}
