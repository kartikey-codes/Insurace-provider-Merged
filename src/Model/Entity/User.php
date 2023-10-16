<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Lib\DateUtility\DateUtility;
use App\Lib\PeopleUtility\PeopleUtility;
use App\Lib\TokenUtility\TokenUtility;
use Authentication\IdentityInterface;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;

/**
 * User Entity.
 */
class User extends Entity implements IdentityInterface, EntityInterface
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
		'days_until_birthday',
		'full_name',
		'initials',
		'is_birthday',
		'list_name',
	];

	/**
	 * Fields that are not exposed to JSON/Arrays
	 *
	 * @var array
	 */
	protected $_hidden = [
		'api_token',
		'password',
		'confirm_password',
		'password_reset_token',
		'password_reset_token_expiration',
		'lock_expiration',
		'locked',
	];

	/**
	 * Authentication\IdentityInterface method
	 *
	 * @return ?int
	 */
	public function getIdentifier()
	{
		return $this->id;
	}

	/**
	 * Authentication\IdentityInterface method
	 *
	 * @return self
	 */
	public function getOriginalData()
	{
		return $this;
	}

	/**
	 * Hash password when set
	 *
	 * @param string|null $password
	 * @return string
	 */
	protected function _setPassword(?string $password): string
	{
		if ($password == null) {
			return '';
		}

		return (new DefaultPasswordHasher())->hash($password);
	}

	/**
	 * Virtual 'full_name' property
	 *
	 * @return string
	 */
	protected function _getFullName(): string
	{
		return PeopleUtility::combineFullName(
			$this->first_name,
			$this->middle_name,
			$this->last_name,
			null // No title
		);
	}

	/**
	 * Virtual 'list_name' property
	 *
	 * @return string
	 */
	protected function _getListName(): string
	{
		return PeopleUtility::combineListName(
			$this->first_name,
			$this->middle_name,
			$this->last_name,
			null // No title
		);
	}

	/**
	 * Virtual 'initials' property
	 * Returns users initials based on name
	 *
	 * @return ?string
	 */
	protected function _getInitials(): ?string
	{
		return PeopleUtility::getInitials(
			PeopleUtility::combineFullName(
				$this->first_name,
				$this->middle_name,
				$this->last_name,
				null // No title
			)
		);
	}

	/**
	 * Virtual 'is_birthday' property
	 * Is today the users birthday or not
	 *
	 * @return ?bool
	 */
	protected function _getIsBirthday(): ?bool
	{
		return DateUtility::getIsAnniversary($this->date_of_birth);
	}

	/**
	 * Virtual 'days_until_birthday' property
	 * Returns days until next birthday, or null if unprovided
	 *
	 * @return ?int
	 */
	protected function _getDaysUntilBirthday(): ?int
	{
		return DateUtility::getDaysUntilAnniversary($this->date_of_birth);
	}

	/**
	 * Does this user have a client_id/vendor_id or is admin
	 *
	 * @return bool
	 */
	public function hasAssociatedArea(): bool
	{
		return $this->isAdmin() || $this->isClientUser() || $this->isVendorUser();
	}

	/**
	 * Is this user a global admin (staff) or not
	 *
	 * @return bool
	 */
	public function isAdmin(): bool
	{
		return !empty($this->admin);
	}

	/**
	 * Is this user a client organization admin (tenant) or not
	 *
	 * @return bool
	 */
	public function isClientAdmin(): bool
	{
		return !empty($this->client_admin);
	}

	/**
	 * Is this user a client user or not
	 *
	 * @return bool
	 */
	public function isClientUser(): bool
	{
		return !empty($this->client_id);
	}

	/**
	 * Is this user a vendor user or not
	 *
	 * @return bool
	 */
	public function isVendorUser(): bool
	{
		return !empty($this->vendor_id);
	}

	/**
	 * User must change their password
	 *
	 * @return bool
	 */
	public function hasRequiredPasswordChange(): bool
	{
		return $this->get('force_change_password') ? true : false;
	}

	/**
	 * Get API Token
	 *
	 * @return string
	 */
	public function getToken(): string
	{
		if (empty($this->get('id'))) {
			return false;
		}

		return $this->get('api_token');
	}

	/**
	 * Generate New Password
	 *
	 * Returns a new generated password
	 *
	 * @return string
	 */
	public function generateTemporaryPassword(): string
	{
		return TokenUtility::temporaryPassword();
	}

	/**
	 * Generate New API Token
	 *
	 * Returns a new API token
	 *
	 * @return string
	 */
	public function generateApiToken(): string
	{
		return TokenUtility::apiToken();
	}
}
