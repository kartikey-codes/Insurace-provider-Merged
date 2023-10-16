<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Case Types Controller
 *
 * @property \App\Model\Table\CaseTypesTable $CaseTypes
 */
class CaseTypesController extends ApiController
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
			'short_name',
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
		$this->set('data', $this->CaseTypes->get($id));
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
		$entities = $this->CaseTypes
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('CaseTypes', 'all')
			->all();

		$this->set('data', $entities);
	}
}
