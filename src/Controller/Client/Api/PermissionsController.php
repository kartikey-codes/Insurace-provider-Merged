<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Permissions Controller
 *
 * @property \App\Model\Table\PermissionsTable $Permissions
 */
class PermissionsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'category' => 'asc',
			'controller' => 'asc',
			'action' => 'asc',
			'name' => 'asc'
		],
		'sortableFields' => [
			'id',
			'controller',
			'action',
			'category',
			'name',
			'key'
		],
	];

	/**
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$entities = $this->Permissions
			->find('all')
			->find('ordered')
			->cache('Permissions', 'all')
			->all();

		$this->set('data', $entities);
	}
}
