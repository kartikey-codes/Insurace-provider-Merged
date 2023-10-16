<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\StorageUtility\StorageUtility;
use App\Lib\TimeUtility\TimeUtility;
use App\Model\Entity\User;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Incoming Document Entity.
 */
class IncomingDocument extends Entity
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
		'age',
		'preview_url',
		'original_name_base',
		'original_name_extension'
	];

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
	 * Virtual 'preview_url' property
	 * Returns full URL to preview action
	 *
	 * @return ?string
	 */
	protected function _getPreviewUrl(): ?string
	{
		return Router::url([
			'prefix' => 'Client/Api',
			'controller' => 'IncomingDocuments',
			'action' => 'preview',
			$this->id,
		], true);
	}

	/**
	 * Virtual 'original_name_base' property
	 * Returns original filename without extension
	 *
	 * @return ?string
	 */
	protected function _getOriginalNameBase(): ?string
	{
		return StorageUtility::getFileNameWithoutExtension($this->original_name);
	}

	/**
	 * Virtual 'original_name_extension' property
	 * Returns file extension from original filename
	 *
	 * @return ?string
	 */
	protected function _getOriginalNameExtension(): ?string
	{
		return StorageUtility::getFileExtensionFromName($this->original_name);
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
}
