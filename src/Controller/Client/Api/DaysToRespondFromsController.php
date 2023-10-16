<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Days To Respond Froms Controller
 *
 * @property \App\Model\Table\DaysToRespondFromsTable $DaysToRespondFroms
 */
class DaysToRespondFromsController extends ApiController
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
		'fields' => [
			'id',
			'name'
		],
		'sortableFields' => [
			'name',
			'created',
			'modified',
			'deleted',
		],
	];

	/**
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$entities = $this->DaysToRespondFroms
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('DaysToRespondFroms', 'all')
			->all();

		$this->set('data', $entities);
	}
}
