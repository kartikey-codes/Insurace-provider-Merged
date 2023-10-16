<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\UserListener;
use App\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Log Out Controller
 */
class LogoutController extends AppController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\UsersTable
	 */
	public UsersTable $Users;

	/** @var string */
	public const LOGOUT_REASON_TOKEN = 'Unable to retrieve user information. Your account may be disabled.';

	/** @var string */
	public const LOGOUT_REASON_GET_STATE_FAILED = 'Unable to authenticate. Please log in again to continue.';

	/** @var string */
	public const LOGOUT_REASON_INACTIVE = 'You were logged out due to inactivity.';

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
	 * AuthComponent Log Out
	 *
	 * @return void|null|\Cake\Http\Response
	 */
	public function index()
	{
		// Get current user before logging out
		$user = $this->Authentication->getIdentity();

		// Might be empty if session expired
		if (!empty($user)) {
			// Fire logout event for listeners
			$this->getEventManager()->dispatch(
				new Event('Model.User.logout', $this, [
					$user->getOriginalData(),
				])
			);
		}

		// Clear out the existing session
		$this->getRequest()->getSession()->destroy();

		// Tell Authentication we logged out
		$this->Authentication->logout();

		// Handle any reasons we were told to log out with the flash component
		if (!empty($this->getRequest()->getQuery('reason'))) {
			switch ($this->getRequest()->getQuery('reason')) {
					// User's API token was invalid (or reset)
				case 'token':
					$this->Flash->error(__(self::LOGOUT_REASON_TOKEN), 'auth');
					break;
					// Failed to hit the /state endpoint (for app-wide data)
				case 'state':
					$this->Flash->error(__(self::LOGOUT_REASON_GET_STATE_FAILED), 'auth');
					break;
					// User left the application running without any input
				case 'inactive':
					$this->Flash->error(__(self::LOGOUT_REASON_INACTIVE), 'auth');
					break;
			}
		}

		// Finally send us back to the login page
		return $this->redirect(['_name' => 'login']);
	}
}
