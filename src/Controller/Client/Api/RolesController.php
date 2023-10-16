<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Controller\Client\Api\ApiController;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Roles Controller
 *
 * @property \App\Model\Table\RolesTable $Roles
 */
class RolesController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'name' => 'asc'
		],
		'fields' => [
			'id',
			'name',
			'created',
			'created_by',
			'modified',
			'modified_by'
		],
		'sortableFields' => [
			'id',
			'name',
			'created',
			'created_by',
			'modified',
			'modified_by'
		],
		'contain' => [
			'Users',
			'Permissions'
		]
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->Roles->newEntity($this->getRequest()->getData(), [
			'associated' => [
				'Users',
				'Permissions'
			],
		]);

		try {
			$this->Roles->saveOrFail($entity);
			$entity = $this->Roles->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
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
		$this->set('data', $this->Roles->getAll());
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
		$this->set('data', $this->Roles->getFull($id));
	}

	/**
	 * Add user
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function addUser($id): void
	{
		$role = $this->Roles->getFull($id);
		$userId = $this->getRequest()->getData('user_id');
		$user = $this->Roles->Users->get($userId);

		$result = $this->Roles->getAssociation('Users')->link($role, [$user]);

		$this->set('data', $result);
	}

	/**
	 * Remove user
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function removeUser($id): void
	{
		$role = $this->Roles->getFull($id);
		$userId = $this->getRequest()->getData('user_id');
		$user = $this->Roles->Users->get($userId);

		$result = $this->Roles->getAssociation('Users')->unlink($role, [$user]);

		$this->set('data', $result);
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
		$entity = $this->Roles->get($id);

		try {
			$entity = $this->Roles->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => [
					'Users',
					'Permissions'
				],
			]);

			$this->Roles->saveOrFail($entity);
			$entity = $this->Roles->getFull($entity->id);
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
		$entity = $this->Roles->getFull($id);

		try {
			$this->Roles->deleteOrFail($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
