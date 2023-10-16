<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\TimeUtility\TimeUtility;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use InvalidArgumentException;

/**
 * Outgoing Document Entity.
 */
class OutgoingDocument extends Entity
{
	/**
	 * Status constants
	 */
	public const STATUS_NEW = 'NEW';
	public const STATUS_CANCELLED = 'CANCELLED';
	public const STATUS_DELIVERED = 'DELIVERED';
	public const STATUS_FAILED = 'FAILED';
	public const STATUS_QUEUED = 'QUEUED';

	/**
	 * Delivery method constants
	 */
	public const DELIVERY_METHOD_MANUAL = 'MANUAL';
	public const DELIVERY_METHOD_MAIL = 'MAIL';
	public const DELIVERY_METHOD_FAX = 'FAX';
	public const DELIVERY_METHOD_WEB_UPLOAD = 'WEB_UPLOAD';

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
		'age',
		'can_cancel',
		'can_retry',
		'download_url',
		'status_label',
		'delivery_method_label',
		'delivery_method_icon',
		'progress_indeterminate',
		'progress_percent',
		'progress_variant',
	];

	/**
	 * Return if this document can be retried for sending again
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function isRetryable(): bool
	{
		switch ($this->get('status_message')) {
			case self::STATUS_CANCELLED:
			case self::STATUS_FAILED:
				return true; // Can be retried
			default:
				return false;
		}
	}

	/**
	 * Virtual 'age' property
	 * Returns how many seconds since this fax has been received
	 *
	 * @return ?int
	 */
	protected function _getAge(): ?int
	{
		return TimeUtility::getTimeAgoInSeconds($this->created);
	}

	/**
	 * Virtual 'can_cancel' property
	 * Return if entity is eligible for cancelling
	 *
	 * @return bool
	 */
	protected function _getCanCancel(): bool
	{
		switch ($this->get('status_message')) {
			case self::STATUS_NEW:
				return true; // Can be cancelled
			default:
				return false;
		}
	}

	/**
	 * Virtual 'can_retry' property
	 * Return if entity is eligible for retrying
	 *
	 * @return bool
	 */
	protected function _getCanRetry(): bool
	{
		return $this->isRetryable();
	}

	/**
	 * Virtual 'download_url' property
	 * Returns full download URL
	 *
	 * @return ?string
	 */
	protected function _getDownloadUrl(): ?string
	{
		if (empty($this->id)) {
			return null;
		}

		return Router::url([
			'prefix' => 'Client/Api',
			'controller' => 'OutgoingDocuments',
			'action' => 'download',
			$this->id,
		], true);
	}

	/**
	 * Virtual 'status_label' property
	 * Returns friendly name for status
	 *
	 * @return string
	 */
	protected function _getStatusLabel(): string
	{
		switch ($this->status_message) {
			case self::STATUS_NEW:
				return 'Pending';
			case self::STATUS_QUEUED:
				return 'Queued';
			case self::STATUS_CANCELLED:
				return 'Cancelled';
			case self::STATUS_DELIVERED:
				return 'Delivered';
			case self::STATUS_FAILED:
				return 'Failed';
			default:
				return 'Unknown';
		}
	}

	/**
	 * Virtual 'delivery_method_label' property
	 * Returns friendly name for delivery method
	 *
	 * @return string
	 */
	protected function _getDeliveryMethodLabel(): string
	{
		switch ($this->delivery_method) {
			case self::DELIVERY_METHOD_MANUAL:
				return 'Manual';
			case self::DELIVERY_METHOD_MAIL:
				return 'Mail';
			case self::DELIVERY_METHOD_FAX:
				return 'Fax';
			case self::DELIVERY_METHOD_WEB_UPLOAD:
				return 'Web Upload';
			default:
				return 'Unknown';
		}
	}

	/**
	 * Virtual 'delivery_method_icon' property
	 * Returns icon for delivery method
	 *
	 * @return string
	 */
	protected function _getDeliveryMethodIcon(): string
	{
		switch ($this->delivery_method) {
			case self::DELIVERY_METHOD_MANUAL:
				return 'envelope';
			case self::DELIVERY_METHOD_MAIL:
				return 'envelope';
			case self::DELIVERY_METHOD_FAX:
				return 'fax';
			case self::DELIVERY_METHOD_WEB_UPLOAD:
				return 'external-link-alt';
			default:
				return 'external-link-alt';
		}
	}

	/**
	 * Virtual 'progress_indeterminate' property
	 * Returns if the current status is one that may potentially change in the future
	 * based on sending logic
	 *
	 * @return bool
	 */
	protected function _getProgressIndeterminate(): bool
	{
		switch ($this->status_message) {
			case self::STATUS_NEW:
			case self::STATUS_QUEUED:
				return true;
			default:
				return false;
		}
	}

	/**
	 * Virtual 'progress_percent' property
	 * Returns int value of progress percent based on status
	 *
	 * @return int
	 */
	protected function _getProgressPercent(): int
	{
		switch ($this->status_message) {
			case self::STATUS_NEW:
				return 25;
			case self::STATUS_QUEUED:
				return 50;
			case self::STATUS_CANCELLED:
				return 100;
			case self::STATUS_DELIVERED:
				return 100;
			case self::STATUS_FAILED:
				return 75;
			default:
				return 0;
		}
	}

	/**
	 * Virtual 'progress_variant' property
	 * Returns variant for a progress bar based on status
	 *
	 * @return string
	 */
	protected function _getProgressVariant(): string
	{
		switch ($this->status_message) {
			case self::STATUS_NEW:
				return "primary";
			case self::STATUS_QUEUED:
				return "primary";
			case self::STATUS_CANCELLED:
				return "warning";
			case self::STATUS_DELIVERED:
				return "success";
			case self::STATUS_FAILED:
				return "danger";
			default:
				return "";
		}
	}
}
