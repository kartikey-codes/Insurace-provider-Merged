<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Guest Portals Controller
 *
 * @property \App\Model\Table\GuestPortalsTable $GuestPortals
 */
class GuestPortalsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'name' => 'asc',
			'recipient_name' => 'asc',
		],
		'sortableFields' => [
			'name',
			'recipient_name',
			'expiration_date',
			'completed',
			'created',
			'modified',
			'deleted',
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
		$entity = $this->GuestPortals->newEntity($this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->GuestPortals->saveOrFail($entity);
			$entity = $this->GuestPortals->getFull($entity->id);
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
		$this->set('data', $this->GuestPortals->get($id));
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
		$entities = $this->GuestPortals
			->find('all')
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
		$entity = $this->GuestPortals->get($id);

		try {
			$entity = $this->GuestPortals->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => [],
			]);

			$this->GuestPortals->saveOrFail($entity);
			$entity = $this->GuestPortals->getFull($entity->id);
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
		$entity = $this->GuestPortals->getFull($id);

		try {
			$this->GuestPortals->deleteOrFail($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
