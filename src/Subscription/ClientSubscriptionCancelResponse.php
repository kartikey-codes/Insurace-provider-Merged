<?php

declare(strict_types=1);

namespace App\Subscription;

class ClientSubscriptionCancelResponse
{
	public string $provider;
	public string $id;

	public bool $cancelled;
	public string $reason = '';

	public array $metaData = [];
}
