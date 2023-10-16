<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\TenantUtility\TenantUtility;
use App\Model\Table\ClientsTable;

/**
 * Client Settings Controller
 */
class ClientSettingsController extends ApiController
{
	public $modelClass = null;

	private int $id;

	private ClientsTable $Clients;

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

		$this->id = TenantUtility::getTenantIdFromRequest();
		$this->Clients = $this->fetchTable('Clients');
	}

	/**
	 * View method
	 *
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view(): void
	{
		$entity = $this->Clients->get($this->id, [
			'fields' => [
				'id',
				'created',
				'modified',
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
				'contact_department',
				'contact_title',
				'contact_phone',
				'contact_fax',
				'contact_email',
				'status',
				'npi_number',
				'subscription_active',
				'tos_date',
				'primary_taxonomy'
			]
		]);

		$this->set('data', $entity);
	}
}
