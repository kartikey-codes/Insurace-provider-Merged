<?php

declare(strict_types=1);

namespace App\Subscription;

class ClientSubscriptionResponse
{
	public string $provider;
	public string $id;

	public string $name = '';
	public string $description = '';
	public bool $active;
	public int $periodStart;
	public int $periodEnd;
	public string $recurringInterval;
	public float $recurringPrice;
	public string $customerId;
	public string $productId;
	public string $priceId;
	public string $status;
	public string $clientSecret = '';

	public array $metaData;
}
