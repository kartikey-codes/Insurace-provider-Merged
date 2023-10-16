<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * FacilityTypes seed.
 */
class FacilityTypesSeed extends AbstractSeed
{
	/**
	 * Run Method
	 * @return void
	 */
	public function run(): void
	{
		$date = date('Y-m-d H:i:s');

		$data = [
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Hospital',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Skilled Nursing Facility',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Home Health',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Clinic',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Other',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Hospice',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Dialysis',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Imaging/Radiology',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Birth Center',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Nursing Home',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Long-term Care Facility',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Rehab',
			],
		];

		$table = $this->table('facility_types');
		$table->insert($data)->save();
	}
}
