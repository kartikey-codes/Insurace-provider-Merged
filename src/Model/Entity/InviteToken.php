<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Invite Token Entity.
 */
class InviteToken extends Entity
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
		'inviter',
	];

	/**
	 * Virtual 'inviter' property
	 * Returns common fields for clients/vendors
	 * for email templates and such.
	 *
	 * @return object
	 */
	protected function _getInviter(): object
	{
		$appName = Configure::readOrFail('App.name');

		// Used by both clients/vendors
		if (!empty($this->client)) {
			$organization = $this->client->name;
			$id = $this->client->id;
			$type = 'client';
		} elseif (!empty($this->vendor)) {
			$organization = $this->vendor->name;
			$id = $this->vendor->id;
			$type = 'vendor';
		} else {
			$organization = $appName;
			$id = null;
			$type = 'unknown';
		}

		if (!empty($this->created_by_user->full_name)) {
			$name = $this->created_by_user->full_name;
		} else {
			$name = $appName;
		}

		return (object)[
			'id' => $id,
			'name' => $name,
			'organization' => $organization,
			'type' => $type,
		];
	}

	/**
	 * Get API Token
	 *
	 * @return string|null
	 */
	public function getToken(): ?string
	{
		if (empty($this->id)) {
			return null;
		}

		return $this->_fields['token'];
	}

	/**
	 * Generate and set a new Token
	 *
	 * @return void
	 */
	public function setToken(): void
	{
		$this->set('token', $this->generateToken());
	}

	/**
	 * Generate a new invite token
	 *
	 * @return string
	 */
	public function generateToken(): string
	{
		return strtoupper(bin2hex(random_bytes(30)));
	}
}
