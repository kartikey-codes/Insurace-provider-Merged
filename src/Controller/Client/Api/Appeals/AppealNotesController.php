<?php

declare(strict_types=1);

namespace App\Controller\Client\Api\Appeals;

use App\Controller\Client\Api\ApiController;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Appeal Notes Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\AppealNotesTable $AppealNotes
 */
class AppealNotesController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'created' => 'desc',
		],
		'sortableFields' => [
			'priority',
			'created',
			'modified',
			// Associations
			'Users.id',
			'Users.first_name',
			'Users.last_name',
		],
		'contain' => [
			'Appeals',
			'CreatedByUser',
			'ModifiedByUser',
		],
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function add(): void
	{
		$entity = $this->AppealNotes->newEntity($this->getRequest()->getData());
		$entity->set('appeal_id', $this->getRequest()->getParam('appeal_id'));

		try {
			$this->AppealNotes->saveOrFail($entity);
			$entity = $this->AppealNotes->getFull($entity->id);
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
		$entity = $this->AppealNotes->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$entities = $this->AppealNotes
			->find('search', [
				'search' => $this->getRequest()->getQuery(),
			])
			->where([
				$this->AppealNotes->aliasField('appeal_id') => $this->getRequest()->getParam('appeal_id'),
			]);

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->getDefaultTablePagination());
	}

	/**
	 * Delete method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function delete($id): void
	{
		$entity = $this->AppealNotes->getFull($id);

		try {
			$this->AppealNotes->deleteOrFail($entity);

			$this->Log->deleteSuccess($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
