<?php

declare(strict_types=1);

namespace App\Subscription;

class ClientProductResponse
{
	public string $provider;

	/**
	 * 'Price' object for Stripe
     *
     * @var string
	 */
	public string $id;

	public string $name;
	public string $description;
	public bool $active;
	public string $recurringInterval;
	public float $recurringPrice;

	public array $metaData;
}
