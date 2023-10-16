<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Clients Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\ClientsTable $Clients
 */
class ClientsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'name' => 'asc',
		],
		'sortableFields' => [
			'name',
			'email',
			'active',
			'phone',
			'fax',
			'street_address_1',
			'street_address_2',
			'city',
			'state',
			'zip',
			'contact_first_name',
			'contact_last_name',
			'contact_title',
			'contact_email',
			'contact_phone',
			'created',
			'modified',
			'deleted',
			'status',
			// Associations
			'FacilityTypes.name',
		],
	];

	/**
	 * Add method
	 *
	 * @return object
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$this->request->allowMethod(['post']);

		$entity = $this->Clients->newEntity($this->getRequest()->getData(), [
			'associated' => [],
		]);

		$entity->set([
			'status' => 'Active',
			'active' => true,
		]);

		try {
			$this->Clients->saveOrFail($entity);

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

		$entity = $this->Clients->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Index method
	 *
	 * @return array $entities
	 */
	public function index(): void
	{
		$this->request->allowMethod(['get']);

		// Build Query
		$entities = $this->Clients->find(
			'search',
			['search' => $this->getRequest()->getQuery()]
		);

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->request->getAttribute('paging')['Clients']);
	}

	/**
	 * All method
	 *
	 * @return array $entities
	 */
	public function all(): void
	{
		$this->request->allowMethod(['get']);

		$entities = $this->Clients
			->find('all')
			->find('ordered')
			->contain([
				'Owner',
			])
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Active method
	 *
	 * @return array $entities
	 */
	public function active(): void
	{
		$this->request->allowMethod(['get']);

		$entities = $this->Clients
			->find('active')
			->find('ordered')
			->cache('Clients', 'active')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Edit method
	 *
	 * @param string $id
	 * @return object $entity
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function edit($id): void
	{
		$this->request->allowMethod(['patch', 'post', 'put']);

		$entity = $this->Clients->get($id);

		try {
			$requestData = $this->getRequest()->getData();
			if (isset($requestData['status'])) {
				$requestData['active'] = $requestData['status'] === 'Active';
			}

			$entity = $this->Clients->patchEntity($entity, $requestData, [
				'associated' => [],
			]);

			$this->Clients->saveOrFail($entity);
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
	 * @return object $entity
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function delete($id): void
	{
		$this->request->allowMethod(['post', 'delete']);

		$entity = $this->Clients->get($id);

		try {
			$this->Clients->deleteOrFail($entity);

			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Types method
	 *
	 * @return array $entities
	 */
	public function types(): void
	{
		$this->request->allowMethod(['get']);

		$entities = $this->Clients
			->ClientTypes
			->find('all')
			->find('ordered')
			->all();

		$this->set('data', $entities);
	}
}
