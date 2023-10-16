<?php

declare(strict_types=1);

namespace App\Controller\Vendor;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

/**
 * Vendor App Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \App\Controller\Component\PermissionComponent $Permission
 */
class AppController extends Controller
{
	/**
	 * Reference to the current user
     *
     * @var null|\App\Controller\Vendor\IdentityInterface
	 */
	public $currentUser;

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
		$this->currentUser = $this->Authentication->getIdentity();

		// Core 'RequestHandler' component
		$this->loadComponent('RequestHandler');
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
			->setLayout('vendor');
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
			->setLayout('vendor');
	}
}
