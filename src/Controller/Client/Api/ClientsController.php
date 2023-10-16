<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use Authorization\Exception\ForbiddenException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Clients Controller
 *
 * Used for admins to use Client selector.
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\ClientsTable $Clients
 */
class ClientsController extends ApiController
{
	use LocatorAwareTrait;

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
			'email',
			'active',
			'phone',
			'fax',
			'street_address_1',
			'street_address_2',
			'city',
			'state',
			'zip',
			'contact_first_name',
			'contact_last_name',
			'contact_title',
			'contact_email',
			'contact_phone',
			'created',
			'modified',
			'deleted',
			'status',
		],
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

		$identity = $this->Authentication->getIdentity();
		/** @var \App\Model\Entity\User */
		$user = $identity->getOriginalData();

		// Only admins should see client list
		if (!$user->isAdmin()) {
			throw new ForbiddenException();
		}
	}

	/**
	 * Active method
	 *
	 * @return void
	 */
	public function active(): void
	{
		$entities = $this->Clients
			->find('active')
			->find('ordered')
			->cache('Clients', 'active')
			->all();

		$this->set('data', $entities);
	}
}
