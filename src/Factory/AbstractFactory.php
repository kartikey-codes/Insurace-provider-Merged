<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Entity\Client;
use Cake\ORM\Locator\LocatorAwareTrait;
use Faker\Factory;
use Faker\Generator;

abstract class AbstractFactory implements FactoryInterface
{
	use LocatorAwareTrait;

	/**
	 * @var Faker\Generator
	 */
	protected Generator $faker;

	/**
	 * @var null|Client
	 */
	protected ?Client $client = null;

	/**
	 * Constructor
	 *
	 * @param null|Client $client The tenant these records are being generated for
	 * @return void
	 */
	public function __construct()
	{
		$this->faker = Factory::create('en_US');
	}

	/**
	 * @inheritDoc
	 */
	public function setClient(Client $client): self
	{
		$this->client = $client;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function hasClient(): bool
	{
		return !empty($this->client);
	}
}
