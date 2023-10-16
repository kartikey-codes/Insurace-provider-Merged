<?php

declare(strict_types=1);

/**
 * App Controller
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use AuditStash\Meta\RequestMetadata;
use Authentication\IdentityInterface;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/4/en/controllers.html#the-app-controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \App\Controller\Component\PermissionComponent $Permission
 */
class AppController extends Controller
{
	/**
	 * The primary key field on the User model
	 *
	 * @var string
	 */
	public $userPrimaryKey = 'id';

	/**
	 * The unique username/email field on the User model for logging in
	 *
	 * @var string
	 */
	public $userNameField = 'email';

	/**
	 * The unique email address field on the User model for communications
	 *
	 * @var string
	 */
	public $userEmailField = 'email';

	/**
	 * The hashed password field on the User model
	 *
	 * @var string
	 */
	public $passwordField = 'password';

	/**
	 * The confirmed password field (not on the User model)
	 *
	 * @var string
	 */
	public $confirmPasswordField = 'confirm_password';

	/**
	 * The field on the User model that indicates a password change is required
	 *
	 * @var string
	 */
	public $forcePasswordChangeField = 'force_change_password';

	/**
	 * The unique API token field on the User model
	 *
	 * @var string
	 */
	public $apiTokenField = 'api_token';

	/**
	 * The currently logged in user
	 *
	 * @var null|\Authentication\IdentityInterface
	 */
	public ?IdentityInterface $currentUser;

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

		// Authentication Plugin
		$this->loadComponent('Authentication.Authentication');

		// Authorization Plugin
		//$this->loadComponent('Authorization.Authorization');

		// Store current user
		$identity = $this->Authentication->getIdentity();
		/** @var \App\Controller\App\Model\Entity\User */
		$user = !empty($identity) ? $identity->getOriginalData() : null;
		$this->currentUser = $user;

		// App 'Permission' Component
		$this->loadComponent('Permission', [
			'user' => $this->currentUser,
		]);

		// Core 'Flash' Component
		$this->loadComponent('Flash');

		// Core 'Request Handler' Component
		$this->loadComponent('RequestHandler', [
			'enableBeforeRedirect' => false,
		]);
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

		// https://github.com/lorenzo/audit-stash#storing-the-logged-in-user
		$currentUserId = !empty($this->currentUser) ? $this->currentUser->{$this->userPrimaryKey} : null;

		EventManager::instance()->on(
			new RequestMetadata($this->getRequest(), $currentUserId)
		);
	}

	/**
	 * Before render callback.
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		// App Name
		$this->set('_appName', Configure::readOrFail('App.name'));

		// CSRF token is unused
		$this->set('_csrfToken', null);

		// Include user if we're logged in
		$identity = $this->Authentication->getIdentity();

		/** @var \App\Model\Entity\User|null */
		$user = !empty($identity) ? $identity->getOriginalData() : null;

		$this->set('_loggedIn', !empty($identity));
		$this->set('_user', $user);

		$isClientOwner = (!empty($user)) ? $user->isAdmin() : false;

		// Set Client/Vendor Variable
		if (!empty($user)) {
			if ($user->isClientUser()) {
				$clients = $this->fetchTable('Clients');
				$client = $clients->findById($user->client_id)
					->firstOrFail();

				$isClientOwner = $client->owner_user_id == $user->id;

				$this->set('_client', $client);
			}

			if ($user->isVendorUser()) {
				$vendors = $this->fetchTable('Vendors');
				$vendor = $vendors->removeBehavior('Vendor')
					->findById($user->vendor_id)
					->firstOrFail();

				$this->set('_vendor', $vendor);
			}
		}

		// Config exposed to the SPA on page load (feature flags, etc.)
		// Must not contain any sensitive information
		$this->set('_appConfig', [
			'isClientOwner' => (bool)$isClientOwner,
			'subscriptionsEnabled' => (bool)Configure::read('Subscriptions.enabled'),
			'licensingEnabled' => (bool)Configure::read('Subscriptions.licensingEnabled')
		]);

		// Place API token into the view for Vue / Axios
		$this->set('_apiToken', $user->{$this->apiTokenField} ?? '');

		// SSL upgrade insecure http requests to https
		$this->set('_sslUpgradeInsecure', Configure::readOrFail('SSL.upgradeInsecureRequests'));

		// Set the serialize variable if we requested json or xml.
		if (
			!array_key_exists('serialize', $this->viewBuilder()->getOptions()) &&
			in_array($this->getResponse()->getType(), ['application/json', 'application/xml'])
		) {
			$this->viewBuilder()->setOption('serialize', true);
		}
	}
}
