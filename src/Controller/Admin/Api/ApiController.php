<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Authentication\IdentityInterface;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;

/**
 * API Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 */
class ApiController extends Controller
{
	/**
	 * The primary key field on the User model
     *
     * @var string
	 */
	public $userPrimaryKey = 'id';

	/**
	 * The unique username field on the User model for logging in
     *
     * @var string
	 */
	public $userNameField = 'email';

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
	 * Reference to the current user
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
		$this->loadComponent('Authentication.Authentication', [
			'loginRedirect' => false,
			'logoutRedirect' => false,
		]);

		// Authorization Plugin
		$this->loadComponent('Authorization.Authorization');

		// Get reference to the current authenticated user
		$this->currentUser = $this->Authentication->getIdentity()->getOriginalData();

		// Core 'RequestHandler' component
		$this->loadComponent('RequestHandler');

		// App 'Permission' component
		$this->loadComponent('Permission', [
			'user' => !empty($this->currentUser) ? $this->currentUser : null,
		]);

		// App 'ApiError' component
		$this->loadComponent('ApiError');

		// App 'Log' Component
		$this->loadComponent('Log');

		// App 'User Activity' Component
		$this->loadComponent('UserActivity', [
			'modelName' => 'Users',
			'primaryKey' => $this->userPrimaryKey,
		]);

		// Force json responses
		$this->RequestHandler->renderAs($this, 'json');

		$this->setResponse(
			$this->getResponse()->withType('application/json')
		);

		$this->viewBuilder()->setOption('serialize', true);
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
	}
}
