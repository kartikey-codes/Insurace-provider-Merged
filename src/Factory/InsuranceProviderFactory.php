<?php

declare(strict_types=1);

namespace App\Factory;

use App\Lib\LocationUtility\LocationUtility;
use App\Model\Entity\InsuranceProvider;
use App\Model\Table\InsuranceTypesTable;
use Faker\Provider\en_US\Address;
use InvalidArgumentException;
use LengthException;

/**
 * @property \Faker\Generator $faker
 */
class InsuranceProviderFactory extends AbstractFactory
{
	/** @var InsuranceTypesTable */
	private InsuranceTypesTable $insuranceTypes;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->insuranceTypes = $this->fetchTable('InsuranceTypes');
	}

	/**
	 * Generate a faked insurance provider for use in testing or demo data
	 *
	 * @return InsuranceProvider
	 * @throws InvalidArgumentException
	 * @throws LengthException
	 */
	public function fake(): InsuranceProvider
	{
		return new InsuranceProvider([
			'name' => $this->faker->company(),
			'default_insurance_type_id' => $this->insuranceTypes->getRandomFromAll()->id,
			'phone' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'fax' => $this->faker->boolean() ? $this->faker->phoneNumber() : null,
			'street_address_1' => Address::buildingNumber() . ' ' . $this->faker->streetName() . ' ' . $this->faker->streetSuffix(),
			'street_address_2' => $this->faker->boolean() ? Address::secondaryAddress() : null,
			'city' => $this->faker->city(),
			'state' => array_rand(LocationUtility::states()),
			'zip' => Address::postcode(),
			'active' => true
		]);
	}
}
