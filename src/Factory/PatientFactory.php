<?php

declare(strict_types=1);

namespace App\Factory;

use App\Lib\LocationUtility\LocationUtility;
use App\Lib\PeopleUtility\PeopleUtility;
use App\Model\Entity\Patient;
use Faker\Provider\en_US\Address;
use InvalidArgumentException;
use LengthException;

/**
 * @property \Faker\Generator $faker
 */
class PatientFactory extends AbstractFactory
{
	/**
	 * Generate a faked patient for use in testing or demo data
	 *
	 * @return Patient
	 * @throws InvalidArgumentException
	 * @throws LengthException
	 */
	public function fake(): Patient
	{
		// For faker locale
		$gender = rand(0, 1) ? 'Male' : 'Female';

		return new Patient([
			'first_name' => $this->faker->firstName(strtolower($gender)),
			'middle_name' => $this->faker->boolean() ? $this->faker->firstName(strtolower($gender)) : null,
			'last_name' => $this->faker->lastName(),
			'date_of_birth' => $this->faker->date(),
			'sex' => array_rand(PeopleUtility::getGenders()),
			'marital_status' => array_rand(PeopleUtility::getMaritalStatuses()),
			'phone' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'fax' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'street_address_1' => Address::buildingNumber() . ' ' . $this->faker->streetName() . ' ' . $this->faker->streetSuffix(),
			'street_address_2' => $this->faker->boolean() ? Address::secondaryAddress() : null,
			'city' => $this->faker->city(),
			'state' => array_rand(LocationUtility::states()),
			'zip' => Address::postcode(),
			'email' => $this->faker->email(),
			'secured' => $this->faker->boolean(),
			'medical_record_number' => $this->faker->bothify('FAKE-#??####'),
			'ssn_last_four' => $this->faker->numerify('####')
		]);
	}
}
