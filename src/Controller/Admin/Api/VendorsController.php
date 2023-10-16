<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Vendors Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\VendorsTable $Vendors
 */
class VendorsController extends ApiController
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
			'created',
			'modified',
			'deleted',
			'status',
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

		$entity = $this->Vendors->newEntity([
			'status' => 'Active',
			'active' => true,
		] + $this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->Vendors->saveOrFail($entity);

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

		$entity = $this->Vendors->getFull($id);

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
		$entities = $this->Vendors->find(
			'search',
			['search' => $this->getRequest()->getQuery()]
		);

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->request->getAttribute('paging')['Vendors']);
	}

	/**
	 * All method
	 *
	 * @return array $entities
	 */
	public function all(): void
	{
		$this->request->allowMethod(['get']);

		$entities = $this->Vendors
			->find('all', [
				'skipTenantCheck' => true,
			])
			->find('ordered')
			->contain([
				'Owner',
				'Specialties',
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

		$entities = $this->Vendors
			->find('active')
			->find('ordered')
			->cache('Vendors', 'active')
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

		$entity = $this->Vendors->get($id);

		try {
			$requestData = $this->getRequest()->getData();
			if (isset($requestData['status'])) {
				$requestData['active'] = $requestData['status'] === 'Active';
			}

			$entity = $this->Vendors->patchEntity($entity, $requestData, [
				'associated' => [],
			]);

			$this->Vendors->saveOrFail($entity);
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

		$entity = $this->Vendors->get($id);

		try {
			$this->Vendors->deleteOrFail($entity);

			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
