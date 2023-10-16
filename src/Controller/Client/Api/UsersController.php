<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\TenantUtility\TenantUtility;
use Cake\Http\Exception\BadRequestException;
use Cake\Log\Log;
use Cake\ORM\Exception\MissingEntityException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Users Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_HIGH_LIMIT,
		'order' => [
			'first_name' => 'asc',
		],
		'sortableFields' => [
			'id',
			'first_name',
			'middle_name',
			'last_name',
			'email',
			'phone',
			'fax',
			'active',
			'date_of_birth',
			'password_changed',
			'last_login',
			'last_login_ip',
			'last_seen',
			'created',
			'modified',
			// Virtual
			'full_name',
			'list_name',
			// Associations
		],
		'contain' => [
			'Roles' => [
				'finder' => 'ordered'
			]
		],
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->Users->newEntity($this->getRequest()->getData());

		try {
			$this->Users->saveOrFail($entity, [
				'associated' => [
					'Roles'
				],
			]);

			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * View method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view($id): void
	{
		$entity = $this->Users->getFull($id);
		if ($entity->admin && !TenantUtility::isAdmin()) {
			throw new MissingEntityException(); // cannot view admin user if logged in user is not an admin
		}
		$this->set('data', $entity);
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->crudIndex();
	}

	/**
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$entities = $this->Users
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('Users', 'all')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Active method
	 *
	 * @return void
	 */
	public function active(): void
	{
		$entities = $this->Users
			->find('active')
			->find('ordered')
			->find('limited')
			->cache('Users', 'active')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Online method
	 *
	 * @return void
	 */
	public function online()
	{
		$entities = $this->Users
			->find('online')
			->find('ordered')
			->find('limited')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Return a list of newly registered users who does not belong to a vendor or client
	 * Only admin should be able to call this method, it cut across all clients/tenants
	 *
	 * @return void
	 */
	public function new()
	{
		$entities = $this->Users->find(
			'search',
			[
				'search' => $this->getRequest()->getQuery(),
				'skipTenantCheck' => $this->currentUser->isAdmin(),
			]
		)
			->find('new')
			->find('ordered')
			->find('limited')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Edit method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function edit($id): void
	{
		/**
		 * @todo Permission Check
		 * @permissions
		 */

		$entity = $this->Users->get($id);

		try {
			$entity = $this->Users->patchEntity($entity, $this->getRequest()->getData(), [
				'fieldList' => [
					'first_name',
					'last_name',
					'email',
					'phone',
					'active',
				],
			]);

			$this->Users->saveOrFail($entity, [
				'associated' => [
					'Roles',
				],
			]);

			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Set User Password method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function setPassword($id)
	{
		/**
		 * @todo Permission Check
		 * @permissions
		 */
		$entity = $this->Users->get($id);
		$entity = $this->Users->patchEntity($entity, $this->getRequest()->getData(), [
			'fieldList' => [
				'password',
				'confirm_password',
			],
		]);

		try {
			$this->Users->saveOrFail($entity);

			Log::notice(__(
				'User #{0} `{1}` changed password for user #{2} `{3}`',
				$this->currentUser->id,
				$this->currentUser->full_name,
				$entity->id,
				$entity->full_name
			), 'general');

			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Mark a user as active (enabling login)
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function enable($id)
	{
		/**
		 * @todo Permission Check
		 * @permissions
		 */

		$entity = $this->Users->get($id);
		$entity->set('active', true);
		$this->Users->saveOrFail($entity);
		$entity = $this->Users->getFull($id);

		Log::warning(__(
			'User #{0} `{1}` enabled user #{2} `{3}`',
			$this->currentUser->id,
			$this->currentUser->full_name,
			$entity->id,
			$entity->full_name
		), 'general');

		$this->set('data', $entity);
	}

	/**
	 * Mark a user as inactive (disabling login)
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function disable($id)
	{
		/**
		 * @todo Permission Check
		 * @permissions
		 */

		$entity = $this->Users->get($id);
		$entity->set('active', false);
		$this->Users->saveOrFail($entity);
		$entity = $this->Users->getFull($id);

		Log::warning(__(
			'User #{0} `{1}` disabled user #{2} `{3}`',
			$this->currentUser->id,
			$this->currentUser->full_name,
			$entity->id,
			$entity->full_name
		), 'general');

		$this->set('data', $entity);
	}

	/**
	 * Unlock a user after too many failed login attempts
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function unlock($id)
	{
		/**
		 * @todo Permission Check
		 * @permissions
		 */

		$entity = $this->Users->get($id);
		$entity->set('locked', false);
		$this->Users->saveOrFail($entity);
		$entity = $this->Users->getFull($id);

		Log::warning(__(
			'User #{0} `{1}` unlocked user #{2} `{3}`',
			$this->currentUser->id,
			$this->currentUser->full_name,
			$entity->id,
			$entity->full_name
		), 'general');

		$this->set('data', $entity);
	}

	/**
	 * Delete method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function delete($id): void
	{
		/**
		 * @todo Permission Check
		 * @permissions
		 */

		/** @var \App\Model\Entity\User */
		$entity = $this->Users->get($id);

		if ($entity->id == $this->currentUser->id) {
			throw new BadRequestException(__('You cannot delete yourself.'));
		}

		if ($entity->isAdmin()) {
			throw new BadRequestException(__('Admin users cannot be deleted. Revoke admin permissions first in order to delete this user.'));
		}

		try {
			$this->Users->deleteOrFail($entity);
			$this->Log->deleteSuccess($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
