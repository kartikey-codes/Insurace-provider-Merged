<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Entity\ClientEmployee;
use App\Model\Table\FacilitiesTable;
use Faker\Provider\en_US\Address;
use InvalidArgumentException;
use LengthException;

/**
 * @property \Faker\Generator $faker
 */
class ClientEmployeeFactory extends AbstractFactory
{
	/** @var FacilitiesTable */
	private FacilitiesTable $facilities;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->facilities = $this->fetchTable('Facilities');
	}

	/**
	 * Generate a faked client employee for use in testing or demo data
	 *
	 * @return ClientEmployee
	 * @throws InvalidArgumentException
	 * @throws LengthException
	 */
	public function fake(): ClientEmployee
	{
		// For faker locale
		$gender = rand(0, 1) ? 'Male' : 'Female';

		return new ClientEmployee([
			'facility_id' => $this->facilities->getRandomByClient($this->client->id),
			'first_name' => $this->faker->firstName(strtolower($gender)),
			'last_name' => $this->faker->lastName(),
			'title' => $this->faker->title(strtolower($gender)),
			'work_phone' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'mobile_phone' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'email' => $this->faker->email(),
			'npi_number' => $this->faker->numerify('##########'),
			'state' => Address::stateAbbr(),
			'active' => true
		]);
	}
}
