<?php

declare(strict_types=1);

namespace App\Controller\Component;

use App\Model\Table\UsersTable;
use Cake\Controller\Component;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * User Activity Component
 *
 * Handles updating LastSeen timestamp for users
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class UserActivityComponent extends Component
{
	use LocatorAwareTrait;

	/**
     * @var \App\Model\Table\UsersTable
     */
	public UsersTable $Users;

	/**
	 * Default config
	 *
	 * These are merged with user-provided config when the component is used.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [
		'userModel' => 'Users',
		'primaryKey' => 'id',
		'lastSeenField' => 'last_seen',
		'threshold' => '30 seconds', // Minimum time between updates
	];

	/**
	 * Other components that this component relies on
	 *
	 * @var array
	 */
	protected $components = [
		'Authentication',
	];

	/**
	 * Initialization
	 *
	 * @param array $config Array of config options.
	 */
	public function initialize(array $config): void
	{
		// Call Cake component initialization
		parent::initialize($config);

		// Load user model
		$this->Users = $this->fetchTable('Users');
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
		// Get identity from authentication component
		$identity = $this->Authentication->getIdentity();
		if (empty($identity)) {
			return;
		}

		$user = $identity->getOriginalData();
		$userId = $identity->getIdentifier();

		// Don't do anything if we don't find an ID
		if (empty($userId)) {
			return;
		}

		// Get user's current last seen timestamp
		$lastSeen = $user->{$this->getConfig('lastSeenField')};

		// Rate limit updates
		if (!empty($lastSeen) && $lastSeen instanceof FrozenTime) {
			if ($lastSeen->wasWithinLast($this->getConfig('threshold'))) {
				return;
			}
		}

		// Update our last seen timestamp for this user
		$this->Users->updateLastSeen($userId);
	}
}
