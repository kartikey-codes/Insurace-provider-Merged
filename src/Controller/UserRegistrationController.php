<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\UserListener;
use App\Form\UserRegisterForm;
use App\Lib\PasswordUtility\PasswordUtility;
use App\Model\Entity\InviteToken;
use App\Model\Entity\User;
use App\Model\Table\InviteTokensTable;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * User Registration Controller
 *
 * Allows registration of a user account.
 *
 * @property \App\Controller\Component\LogComponent $Log
 */
class UserRegistrationController extends AppController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\UsersTable
	 */
	private UsersTable $Users;

	/**
	 * @var \App\Model\Table\InviteTokensTable
	 */
	private InviteTokensTable $InviteTokens;

	/**
	 * @var string
	 */
	public const REGISTRATION_DISABLED_ERROR = 'Registration is disabled.';

	/**
	 * @var string
	 */
	public const USER_REGISTER_ERROR = 'Please check for errors and try again.';

	/**
	 * @var string
	 */
	public const USER_REGISTER_SUCCESS = 'Your account has been created!';

	/**
	 * @var string
	 */
	public const INVITE_TOKEN_INVALID = 'The invite token used was invalid or already used.';

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

		// Allow registration when not logged in
		$this->Authentication->allowUnauthenticated(['index']);

		// Load models needed
		$this->Users = $this->fetchTable('Users');
		$this->InviteTokens = $this->fetchTable('InviteTokens');

		// App 'Log' Component
		$this->loadComponent('Log');

		// Request Handler
		$this->loadComponent('RequestHandler');

		// Events
		$this->getEventManager()->on(new UserListener());
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
		$registrationEnabled = Configure::readOrFail('Registration.enabled');

		if (!$registrationEnabled) {
			$this->Flash->error(__(self::REGISTRATION_DISABLED_ERROR), 'auth');

			$this->redirect([
				'_name' => 'login',
			]);

			return;
		}
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

		$this->viewBuilder()->setLayout('login');
	}

	/**
	 * User Registration
	 *
	 * @return void
	 */
	public function index(): void
	{
		$form = new UserRegisterForm();
		$inviteToken = $this->request->getQuery('inviteToken', '');
		$minPasswordLength = PasswordUtility::getMinimumLength();

		$this->set(compact(
			'form',
			'inviteToken',
			'minPasswordLength'
		));

		if ($this->request->is('post')) {
			if (!empty($inviteToken) && !$this->InviteTokens->isUsable($inviteToken)) {
				$this->Flash->error(__(self::INVITE_TOKEN_INVALID));

				return;
			}

			if ($form->execute($this->request->getData())) {
				$this->registerUser();
				// Redirect to associated area or registration
				$this->Flash->success(__(self::USER_REGISTER_SUCCESS));
				$this->redirect(['_name' => 'redirector']);
			} else {
				$this->Flash->error(__(self::USER_REGISTER_ERROR));
			}
		}
	}

	/**
	 * Create user account, emit event for email sending, and log in
	 * as the newly created user account
	 *
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 * @return void
	 */
	private function registerUser(): void
	{
		/**
		 * This behavior isn't skipping properly
		 *
		 * @todo Fix the skip tenant/vendor check validation always going off when registering
		 */
		if ($this->Users->hasBehavior('Multitenancy')) {
			$this->Users->removeBehavior('Multitenancy');
		}

		/**
		 * User entity to persist
		 *
		 * @var \App\Model\Entity\User $user
		 */
		$user = $this->Users->newEntity([
			'first_name' => $this->request->getData('first_name'),
			'middle_name' => $this->request->getData('middle_name'),
			'last_name' => $this->request->getData('last_name'),
			'email' => $this->request->getData('email'),
			'password' => $this->request->getData('password'),
			'confirm_password' => $this->request->getData('confirm_password'),
			'active' => true,
			'admin' => false,
		], [
			'validate' => false,
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		// Handle invite token
		$tokenValue = $this->request->getData('invite_token', '');
		if (!empty($tokenValue)) {
			$token = $this->InviteTokens->getByTokenValue($tokenValue);
			$user = $this->applyInviteToken($user, $token);
		}

		// Save user to database
		$user = $this->Users->saveOrFail($user, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		// Emit events for listeners
		$this->getEventManager()->dispatch(
			new Event('Model.User.registered', $this, [
				$user,
			])
		);

		// Fetch all fields for newly created user
		$fullUser = $this->Users->get($user->id, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		// Manually log in as newly registered user
		$this->Authentication->setIdentity($fullUser);
	}

	/**
	 * Handle setting user info
	 *
	 * @param \App\Model\Entity\User $user
	 * @param \App\Model\Entity\InviteToken $token
	 * @return \App\Model\Entity\User
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \Cake\Datasource\Exception\PersistenceFailedException
	 */
	private function applyInviteToken(User $user, InviteToken $token): User
	{
		if (!empty($token->client->id)) {
			$user->set('client_id', $token->client->id);
		}

		if (!empty($token->vendor->id)) {
			$user->set('vendor_id', $token->vendor->id);
		}

		$token->set('active', false);
		$this->InviteTokens->saveOrFail($token);

		return $user;
	}
}
