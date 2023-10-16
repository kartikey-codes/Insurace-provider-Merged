<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Entity\Client;
use Cake\Datasource\EntityInterface;

interface FactoryInterface
{
	/**
	 * Generate a new entity with data from Faker
	 * @return EntityInterface
	 */
	public function fake(): EntityInterface;

	/**
	 * Set client associated with the factory data
	 * @param Client $client
	 * @return $this
	 */
	public function setClient(Client $client): self;

	/**
	 * Check if this factory has an associated client
	 * @return bool
	 */
	public function hasClient(): bool;
}
