<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Audit Reviewers Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\AuditReviewersTable $AuditReviewers
 */
class AuditReviewersController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'id' => 'desc',
		],
		'sortableFields' => [
			'id',
			'agency_id',
			'first_name',
			'middle_name',
			'last_name',
			'title',
			'phone',
			'email',
			'professional_degree',
			'active',
			// Virtual
			'full_name',
			'list_name',
			// Associations
		],
		'contain' => [
			'Agencies'
		]
	];

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();
	}

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->AuditReviewers->newEntity($this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->AuditReviewers->saveOrFail($entity);
			$entity = $this->AuditReviewers->getFull($entity->id);
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
		$this->set('data', $this->AuditReviewers->getFull($id));
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
	 * Active method
	 *
	 * @return void
	 */
	public function active(): void
	{
		$entities = $this->AuditReviewers
			->find('active')
			->find('ordered')
			->find('limited')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$entities = $this->AuditReviewers
			->find('all')
			->find('ordered')
			->cache('AuditReviewers', 'all')
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
		$entity = $this->AuditReviewers->get($id);
		$entity = $this->AuditReviewers->patchEntity($entity, $this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->AuditReviewers->saveOrFail($entity);
			$entity = $this->AuditReviewers->getFull($id);
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
		$entity = $this->AuditReviewers->get($id);

		try {
			$this->AuditReviewers->deleteOrFail($entity);
			$this->Log->deleteSuccess($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
