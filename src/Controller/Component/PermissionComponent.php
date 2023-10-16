<?php

declare(strict_types=1);

namespace App\Controller\Component;

use App\Exception\PermissionDeniedException;
use Cake\Controller\Component;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;

class PermissionComponent extends Component
{
	use LocatorAwareTrait;

	/**
	 * Default config
	 *
	 * These are merged with user-provided config when the component is used.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [];

	/**
	 * Other components that this component relies on
	 *
	 * @var array
	 */
	protected $components = [
		'Authentication',
	];

	/**
	 * Current user to compare against
	 *
	 * @var \Authentication\IdentityInterface|null
	 */
	protected $user;

	/**
	 * Initialization
	 *
	 * @param array $config Array of config options.
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);

		$this->user = $this->getConfig('user');
	}

	/**
	 * Allowed Method
	 *
	 * Used to determine if the current user is allowed to access a specific action.
	 * Generally for pre-checking and passing to the view layer.
	 *
	 * @todo Reimplement based on pre-defined roles
	 * @param string $controller The controller name
	 * @param string $action The action name
	 * @return bool
	 */
	public function allowed(string $controller, string $action): bool
	{
		// Build a request from the controller and action
		$attemptedRequest = (new ServerRequest())
			->withParam('controller', $controller)
			->withParam('action', $action)
			->withParam('plugin', false);

		return true;
	}

	/**
	 * Required By Key Method
	 *
	 * Used in a controller to enforce a permission for the
	 * current user.
	 *
	 * @todo Make a simple string key for permissions, like 'patients:add'
	 *
	 * @param string $key The unique permission name
	 * @return void
	 * @throws PermissionDeniedException
	 */
	public function required(string $key): void
	{
		if ($key == 'fail') {
			throw new PermissionDeniedException();
		}
	}
}
