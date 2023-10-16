<?php

declare(strict_types=1);

namespace App\Factory;

use App\Lib\LocationUtility\LocationUtility;
use App\Model\Entity\Facility;
use App\Model\Table\FacilityTypesTable;
use Faker\Provider\en_US\Address;
use InvalidArgumentException;
use LengthException;

/**
 * @property \Faker\Generator $faker
 */
class FacilityFactory extends AbstractFactory
{
	/** @var FacilityTypesTable */
	private FacilityTypesTable $facilityTypes;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->facilityTypes = $this->fetchTable('FacilityTypes');
	}

	/**
	 * Generate a faked facility for use in testing or demo data
	 *
	 * @return Facility
	 * @throws InvalidArgumentException
	 * @throws LengthException
	 */
	public function fake(): Facility
	{
		return new Facility([
			'name' => $this->faker->company(),
			'facility_type_id' => $this->facilityTypes->getRandomFromAll()->id,
			'phone' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'fax' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'email' => $this->faker->email(),
			'street_address_1' => Address::buildingNumber() . ' ' . $this->faker->streetName() . ' ' . $this->faker->streetSuffix(),
			'street_address_2' => $this->faker->boolean() ? Address::secondaryAddress() : null,
			'city' => $this->faker->city(),
			'state' => array_rand(LocationUtility::states()),
			'zip' => Address::postcode(),
			'active' => true
		]);
	}
}
