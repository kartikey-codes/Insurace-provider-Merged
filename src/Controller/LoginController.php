<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\UserListener;
use App\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Login Controller
 */
class LoginController extends AppController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\UsersTable
	 */
	public UsersTable $Users;

	/** @var string */
	public const LOGIN_INVALID = 'Invalid email and password combination.';

	/** @var string */
	public const LOGIN_LOCKED_OUT = 'Your account has been locked out until {0}.';

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
			'index',
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
	 * Application Log In
	 *
	 * @return void|null|\Cake\Http\Response
	 */
	public function index(): void
	{
		// Nothing more to do if we're not attempting to log in
		if (!$this->getRequest()->is(['PATCH', 'POST', 'PUT'])) {
			return;
		}

		// Check for identity with authentication plugin
		$result = $this->Authentication->getResult();

		// If the user is logged in send them away.
		if ($result->isValid()) {
			// Get user from authentication plugin
			$identity = $this->Authentication->getIdentity();

			/** @var \App\Model\Entity\User $user */
			$user = $identity->getOriginalData();

			// Login Event
			$this->getEventManager()->dispatch(
				new Event('Model.User.login', $this, [
					$user,
					[
						'email' => $this->getRequest()->getData('email'),
						'session_id' => $this->getRequest()->getSession()->id(),
					],
				])
			);

			// Force change password option
			if ($user->hasRequiredPasswordChange()) {
				$this->redirect(['_name' => 'forceChangePassword']);

				return;
			}

			// Invite token
			$inviteToken = $this->getRequest()->getQuery('inviteToken', '');
			if (!empty($inviteToken)) {
				$this->redirect([
					'_name' => 'inviteTokenRedeem',
					'?' => [
						'token' => $inviteToken,
					],
				]);

				return;
			}

			// Redirect to dashboard
			$target = $this->Authentication->getLoginRedirect() ?? '/';

			$this->redirect($target);

			return;
		}

		if ($this->request->is('post') && !$result->isValid()) {
			$this->getEventManager()->dispatch(
				new Event('Model.User.failed_login', $this, [
					[
						// username is the param name for Form authentication
						'email' => $this->getRequest()->getData('email'),
						'password' => $this->getRequest()->getData('password'),
					],
					[
						// Options
					],
				])
			);

			// Determine flash message based on locked out or just invalid password.
			$userId = $this->Users->getIdByEmail($this->getRequest()->getData('email'));

			// User does not exist  b
			if (empty($userId)) {
				// Use invalid message to not give away if account is registered
				$this->Flash->error(__(self::LOGIN_INVALID), 'auth');

				return;
			}

			// User is locked out
			$isLockedOut = $this->Users->isEntityLocked($userId);

			if ($isLockedOut) {
				$expiration = $this->Users->getEntityLockExpiration($userId);
				$this->Flash->error(__(self::LOGIN_LOCKED_OUT, $expiration), 'auth');

				return;
			}

			// Default message
			$this->Flash->error(__(self::LOGIN_INVALID), 'auth');

			return;
		}
	}
}
