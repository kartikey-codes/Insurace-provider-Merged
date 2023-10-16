<?php

declare(strict_types=1);

namespace App\Controller\Client\Api\Cases;

use App\Controller\Client\Api\ApiController;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Case Requests Controller
 *
 * This controller is used for actions on a single case request
 * entity. Index and bulk actions should be in the root namespace.
 *
 * @property \App\Model\Table\CaseRequestsTable $CaseRequests
 */
class CaseRequestsController extends ApiController
{
	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->CaseRequests->newEntity($this->getRequest()->getData());

		try {
			$this->CaseRequests->saveOrFail($entity);
			$entity = $this->CaseRequests->getFull($entity->id);
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
		$this->set('data', $this->CaseRequests->getFull($id));
	}

	/**
	 * Assign method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function assign($id): void
	{
		$entity = $this->CaseRequests->get($id);

		$entity->set([
			'assigned' => new FrozenTime(),
			'assigned_to' => $this->getRequest()->getData('user_id'),
		]);

		$this->CaseRequests->saveOrFail($entity);

		$entity = $this->CaseRequests->getFull($id);

		$this->set('data', $entity);
		$this->set('result', true);
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
		$entity = $this->CaseRequests->get($id);

		try {
			$entity = $this->CaseRequests->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => []
			]);

			$this->CaseRequests->saveOrFail($entity);
			$entity = $this->CaseRequests->getFull($entity->id);
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
		$entity = $this->CaseRequests->getFull($id);

		try {
			$this->CaseRequests->deleteOrFail($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Close method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function close($id): void
	{
		$entity = $this->CaseRequests->get($id);

		try {
			$entity->set([
				'completed' => true,
				'completed_at' => new FrozenTime(),
				'completed_by' => $this->currentUser->id
			]);

			$this->CaseRequests->saveOrFail($entity);
			$entity = $this->CaseRequests->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Reopen method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function reopen($id): void
	{
		$entity = $this->CaseRequests->get($id);

		try {
			$entity->set([
				'completed' => false,
				'completed_at' => null,
				'completed_by' => null
			]);

			$this->CaseRequests->saveOrFail($entity);
			$entity = $this->CaseRequests->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}


	/**
	 * UTC (Unable to complete) method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function utc($id): void
	{
		$entity = $this->CaseRequests->get($id);

		try {
			$entity->set([
				'unable_to_complete' => $this->getRequest()->getData('unable_to_complete'),
			]);

			$this->CaseRequests->saveOrFail($entity);
			$entity = $this->CaseRequests->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
