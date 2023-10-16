<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\UserListener;
use App\Exception\UserNotRegisteredException;
use App\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Auth Controller
 *
 * Heavily linked to users
 */
class AuthController extends AppController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\UsersTable
	 */
	public UsersTable $Users;

	/** @var string */
	public const FORCE_PASSWORD_CHANGE_INVALID = 'Please check for errors and try again.';

	/** @var string */
	public const FORGOT_NO_EMAIL = 'No email address was provided.';

	/** @var string */
	public const FORGOT_SUCCESS = 'An email has been sent to you with a link to reset your password.';

	/** @var string */
	public const RESET_TOKEN_INVALID = 'The password reset token provided does not exist or is invalid.';

	/** @var string */
	public const RESET_ERROR = 'Please check for errors and try again.';

	/** @var string */
	public const RESET_SUCCESS = 'Your password has been reset successfully.';

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		// Load user table
		$this->Users = $this->fetchTable('Users');

		// Allow login action when unauthenticated
		$this->Authentication->allowUnauthenticated([
			'direct',
			'forgotPassword',
			'resetPassword',
			'forceChangePassword',
		]);

		// User Events
		$this->getEventManager()->on(
			new UserListener()
		);
	}

	/**
	 * Before filter callback.
	 * Executes before every request.
	 *
	 * @param \Cake\Event\Event $event The beforeFilter event.
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void
	{
		parent::beforeFilter($event);

		$this->viewBuilder()
			->setLayout('login');
	}

	/**
	 * Before render callback.
	 * Executes before the view is rendered
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		$this->viewBuilder()
			->setLayout('login');
	}

	/**
	 * Redirect User
	 *
	 * @return void|null|\Cake\Http\Response
	 */
	public function direct()
	{
		$identity = $this->Authentication->getIdentity();

		if (empty($identity)) {
			return $this->redirect([
				'_name' => 'login',
			]);
		}

		/** @var \App\Model\Entity\User */
		$user = $identity->getOriginalData();

		if ($user->isVendorUser()) {
			return $this->redirect([
				'controller' => 'Spa',
				'action' => 'vendor',
				'prefix' => null,
			]);
		}

		if ($user->isClientUser()) {
			return $this->redirect([
				'controller' => 'Spa',
				'action' => 'client',
				'prefix' => null,
			]);
		}

		// De-emphasize admin area until moved to separate app
		if ($user->isAdmin()) {
			return $this->redirect([
				'controller' => 'Spa',
				'action' => 'client',
				'prefix' => null,
			]);
		}

		// Fallback to login if nowhere to direct to
		$this->Flash->warning(__('Your account is not associated with a team. Please register your organization as a client or vendor, or await an invite to join an existing organization.'));

		return $this->redirect([
			'_name' => 'login',
		]);
	}

	/**
	 * Forgot Password
	 *
	 * @return void|null|\Cake\Http\Response
	 */
	public function forgotPassword()
	{
		if (!$this->getRequest()->is(['PATCH', 'POST', 'PUT'])) {
			return null;
		}

		if (empty($this->getRequest()->getData('email'))) {
			$this->Flash->error(__(self::FORGOT_NO_EMAIL));

			return null;
		}

		try {
			// Set the reset token
			$this->Users->forgotPassword($this->getRequest()->getData('email'));
			$this->Flash->success(__(self::FORGOT_SUCCESS));

			return $this->redirect(['_name' => 'login']);
		} catch (UserNotRegisteredException $e) {
			$this->Flash->error($e->getMessage());
		}
	}

	/**
	 * Reset password method
	 *
	 * Used by users to reset their password with an emailed token.
	 *
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function resetPassword($token)
	{
		// Lookup user by reset token
		$user = $this->Users->find('passwordReset', [
			'token' => $token,
		])->first();

		// Make sure user exists
		if (empty($user)) {
			$this->Flash->error(__(self::RESET_TOKEN_INVALID));

			return $this->redirect(['_name' => 'login']);
		}

		// Save the new POST'd password
		if ($this->getRequest()->is(['PATCH', 'POST', 'PUT'])) {
			$user = $this->Users->patchEntity($user, $this->getRequest()->getData(), [
				'fields' => [
					$this->passwordField,
					$this->confirmPasswordField,
				],
			]);

			// Save the new password
			if ($this->Users->save($user, ['skipTenantCheck' => true])) {
				// Get the full user record and manually log in
				$user = $this->Users->get($user->{$this->userPrimaryKey});
				$this->Authentication->setIdentity($user);
				$this->Flash->success(__(self::RESET_SUCCESS));

				return $this->redirect(['_name' => 'redirector']);
			} else {
				$this->Flash->error(__(self::RESET_ERROR));
			}
		}

		$this->set(compact('user'));
	}

	/**
	 * Force change password method
	 *
	 * Require a user to change their password to complete logging in.
	 *
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function forceChangePassword()
	{
		// Get the user from the database
		$identity = $this->Authentication->getIdentity();

		/** @var \App\Model\Entity\User */
		$user = $identity->getOriginalData();

		// Save the new POST'd password
		if ($this->getRequest()->is(['PATCH', 'POST', 'PUT'])) {
			$user = $this->Users->patchEntity($user, $this->getRequest()->getData(), [
				'fields' => [
					$this->passwordField,
					$this->confirmPasswordField,
				],
			]);

			// Save the new password
			if ($this->Users->save($user, ['skipTenantCheck' => true])) {
				// Get the refreshed user record and manually log in
				$user = $this->Users->get($user->{$this->userPrimaryKey});
				$this->Authentication->setIdentity($user);

				return $this->redirect(['_name' => 'redirector']);
			} else {
				$this->Flash->error(__(self::FORCE_PASSWORD_CHANGE_INVALID));
			}
		}

		$this->set(compact('user'));
	}
}
