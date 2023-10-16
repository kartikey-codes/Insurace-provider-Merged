<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Appeal Types Controller
 *
 * @property \App\Model\Table\AppealTypesTable $AppealTypes
 */
class AppealTypesController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'name' => 'asc',
		],
		'sortableFields' => [
			'name',
			'active',
			'created',
			'modified',
			'deleted',
		],
	];

	/**
	 * View method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view($id): void
	{
		$this->set('data', $this->AppealTypes->get($id));
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
		$entities = $this->AppealTypes
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('AppealTypes', 'all')
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
		$entities = $this->AppealTypes
			->find('active')
			->find('ordered')
			->find('limited')
			->cache('AppealTypes', 'active')
			->all();

		$this->set('data', $entities);
	}
}
