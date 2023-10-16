<?php

declare(strict_types=1);

namespace App\Subscription;

class ClientSubscriptionUpdateResponse
{
	public string $provider;
	public string $id;

	public bool $updated;
	public string $reason = '';

	public array $metaData = [];
}
