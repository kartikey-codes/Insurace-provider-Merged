<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Appeal Levels Controller
 *
 * @property \App\Model\Table\AppealLevelsTable $AppealLevels
 */
class AppealLevelsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'order_number' => 'asc',
			'name' => 'asc',
		],
		'sortableFields' => [
			'name',
			'active',
			'order_number',
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
		$this->set('data', $this->AppealLevels->get($id));
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
		$entities = $this->AppealLevels
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('AppealLevels', 'all')
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
		$entities = $this->AppealLevels
			->find('active')
			->find('ordered')
			->find('limited')
			->cache('AppealLevels', 'active')
			->all();

		$this->set('data', $entities);
	}
}
