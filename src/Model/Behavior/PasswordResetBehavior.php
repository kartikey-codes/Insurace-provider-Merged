<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use App\Exception\UserNotRegisteredException;
use App\Lib\TokenUtility\TokenUtility;
use ArrayObject;
use Cake\Core\Exception\CakeException;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Password Reset Behavior
 *
 * Handles logic for having a password and generating
 * reset tokens for Users.
 */
class PasswordResetBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'checkFields' => false,
		'forgotPasswordEvent' => '', // ex: Model.User.forgot_password
		'fields' => [
			'email' => 'email',
			'username' => 'email', // email is login username
			'password' => 'password',
			'passwordChanged' => 'password_changed',
			'resetToken' => 'password_reset_token',
			'tokenExpiration' => 'password_reset_token_expiration',
		],
		'implementedFinders' => [
			'forgotPassword' => 'findForgotPassword',
			'passwordReset' => 'findPasswordReset',
		],
	];

	/**
	 * @var string
	 */
	private string $primaryKeyField;

	/**
	 * Set up the behavior
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);

		// Get the primary key field we are using to update this specific entity
		// Most likely just 'id'
		$this->primaryKeyField = $this->table()->aliasField($this->table()->getPrimaryKey());

		if ($this->getConfig('checkFields', false)) {
			$this->ensurePasswordBehaviorFieldsExist();
		}
	}

	/**
	 * Check if the configured fields exist on the table this behavior
	 * is attached to. This is optional so the DB isn't being hit
	 * when bootstrapping the application or serving a request that
	 * doesn't need the database.
	 *
	 * @return void
	 * @throws \Cake\Datasource\Exception\MissingDatasourceConfigException
	 * @throws \RuntimeException
	 * @throws \Cake\Database\Exception\DatabaseException
	 * @throws \InvalidArgumentException
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 */
	private function ensurePasswordBehaviorFieldsExist(): void
	{
		// Check for required username field
		if (!$this->table()->hasField($this->getConfig('fields.username'))) {
			throw new CakeException(__(
				'Table "{0}" does not have username field "{1}".',
				$this->table()->getTable(),
				$this->getConfig('fields.username')
			));
		}

		// Check for the reset token field
		if (!$this->table()->hasField($this->getConfig('fields.resetToken'))) {
			throw new CakeException(__(
				'Table "{0}" does not have password reset token field "{1}".',
				$this->table()->getTable(),
				$this->getConfig('fields.resetToken')
			));
		}

		if (!$this->table()->hasField($this->getConfig('fields.passwordChanged'))) {
			throw new CakeException(__(
				'Table "{0}" does not have password changed field "{1}".',
				$this->table()->getTable(),
				$this->getConfig('fields.passwordChanged')
			));
		}

		if (!$this->table()->hasField($this->getConfig('fields.tokenExpiration'))) {
			throw new CakeException(__(
				'Table "{0}" does not have forgot password token expiration field "{1}".',
				$this->table()->getTable(),
				$this->getConfig('fields.tokenExpiration')
			));
		}
	}

	/**
	 * Logic to execute before saving
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		/**
		 * Set password changed timestamp
		 */
		if ($entity->isDirty($this->getConfig('fields.password'))) {
			$entity->set(
				$this->getConfig('fields.passwordChanged'),
				new FrozenTime('now')
			);
		}
	}

	/**
	 * Find User By Password Reset Token
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findPasswordReset(Query $query, array $options): Query
	{
		if (empty($options['token'])) {
			throw new CakeException(__('A password reset token must be provided.'));
		}

		$tokenField = $this->table()->aliasField(
			$this->getConfig('fields.resetToken')
		);

		$query->applyOptions([
			'skipTenantCheck' => true,
		]);

		return $query->where([
			$tokenField => $options['token'],
		]);
	}

	/**
	 * Find User By Email For Forgotten Password
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findForgotPassword(Query $query, array $options): Query
	{
		if (empty($options['email'])) {
			throw new CakeException(__('An email address must be provided.'));
		}

		// Find entity by email
		return $this->table()
			->query()
			->applyOptions(['skipTenantCheck' => true])
			->where([
				$this->getConfig('fields.email') => $options['email'],
			]);
	}

	/**
	 * Find entity by email and initiate the forgotten password process
	 *
	 * @param string $email
	 * @return bool
	 */
	public function forgotPassword(string $email): bool
	{
		// Find user by email address
		$entity = $this->table()->find('forgotPassword', [
			'email' => $email,
			'skipTenantCheck' => true,
		])->first();

		// Throw an exception if the entity (i.e. user) could not be found
		if (empty($entity)) {
			throw new UserNotRegisteredException([
				'email' => $email
			]);
		}

		// Generate a new token for the user, which invalidates old ones.
		$newToken = $this->generateResetToken();

		// Save token and expiration to entity
		$this->applyResetToken($entity, $newToken);

		$eventName = $this->getConfig('forgotPasswordEvent', null);
		if (!empty($eventName)) {
			$this->table()->getEventManager()->dispatch(
				new Event($eventName, $this, [
					// Make sure we send our most updated user information
					$this->table()->get($entity->get($this->table()->getPrimaryKey())),
					[
						// Options
					],
				])
			);
		}

		// Return status
		return true;
	}

	/**
	 * Apply reset token and expiration values to entity.
	 *
	 * Perform an update query to save the token to the entity
	 * This way we don't interfere with callbacks like the created/modified timestamps
	 *
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param string $token
	 * @return bool
	 * @throws \Cake\Core\Exception\CakeException
	 */
	private function applyResetToken(EntityInterface $entity, string $token): bool
	{
		$primaryKeyValue = $entity->get($this->table()->getPrimaryKey());

		$updateQuery = $this->table()
			->query()
			->update()
			->set([
				$this->getConfig('fields.resetToken') => $token,
				$this->getConfig('fields.tokenExpiration') => $this->getResetTokenExpiration(),
			])
			->where([
				$this->primaryKeyField => $primaryKeyValue,
			]);

		if (!$updateQuery->execute()) {
			throw new CakeException(__('Failed to give reset token'));
		}

		return true;
	}

	/**
	 * Generate Password Reset Token
	 *
	 * @param ?string $salt
	 * @return string
	 */
	public function generateResetToken(?string $salt = null): string
	{
		return TokenUtility::passwordReset($salt);
	}

	/**
	 * Get password reset token expiration date
	 *
	 * @return \Cake\I18n\FrozenTime
	 */
	public function getResetTokenExpiration(): FrozenTime
	{
		// Default to 24 hours from now
		return new FrozenTime('tomorrow');
	}
}
