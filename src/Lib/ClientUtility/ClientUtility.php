<?php

declare(strict_types=1);

namespace App\Lib\ClientUtility;

use App\Lib\TenantUtility\TenantUtility;
use Cake\ORM\TableRegistry;

/**
 * Class for handling client (tenant) logic
 */
class ClientUtility
{
	/**
	 * Get client name from current user
	 *
	 * @return string
	 */
	public static function getClientName(): string
	{
		$tenantId = TenantUtility::getTenantIdFromRequest();
		$clients = TableRegistry::getTableLocator()->get('Clients');

		$client = $clients->get($tenantId, [
			'fields' => [
				'name'
			]
		]);

		return $client->name;
	}
}
