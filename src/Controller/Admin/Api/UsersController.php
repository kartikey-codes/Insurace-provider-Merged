<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

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
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'id' => 'desc',
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
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$this->request->allowMethod(['post']);

		$entity = $this->Users->newEntity([
			'status' => 'Active',
			'active' => true,
		] + $this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->Users->saveOrFail($entity);

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
	 * @return object $entity
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view($id): void
	{
		$this->request->allowMethod(['get']);

		$entity = $this->Users->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->request->allowMethod(['get']);

		// Build Query
		$entities = $this->Users->find(
			'search',
			['search' => $this->getRequest()->getQuery()]
		);

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->request->getAttribute('paging')['Users']);
	}

	/**
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$this->request->allowMethod(['get']);

		$entities = $this->Users
			->find('all', [
				'skipTenantCheck' => true,
			])
			->find('ordered')
			->contain([])
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
		$this->request->allowMethod(['get']);

		$entities = $this->Users
			->find('active')
			->find('ordered')
			->cache('Users', 'active')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Online method
	 *
	 * @return void
	 */
	public function online(): void
	{
		$this->request->allowMethod(['get']);

		$entities = $this->Users
			->find('online', [
				'skipTenantCheck' => true,
			])
			->contain([
				'Clients',
				'Vendors',
			])
			->select([
				'Users.id',
				'Users.first_name',
				'Users.last_name',
				'Users.last_seen',
				'Users.admin',
				'Users.client_id',
				'Users.vendor_id',
				'Clients.name',
				'Vendors.name',
			])
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
		$this->request->allowMethod(['patch', 'post', 'put']);

		$entity = $this->Users->get($id);

		try {
			$requestData = $this->getRequest()->getData();
			if (isset($requestData['status'])) {
				$requestData['active'] = $requestData['status'] === 'Active';
			}

			$entity = $this->Users->patchEntity($entity, $requestData, [
				'associated' => [],
			]);

			$this->Users->saveOrFail($entity, [
				'skipTenantCheck' => true,
			]);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
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
		$this->request->allowMethod(['post', 'delete']);

		$entity = $this->Users->get($id);

		try {
			$this->Users->deleteOrFail($entity, [
				'skipTenantCheck' => true,
			]);

			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
