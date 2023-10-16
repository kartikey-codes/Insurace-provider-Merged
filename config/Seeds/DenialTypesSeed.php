<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Denial Types seed.
 */
class DenialTypesSeed extends AbstractSeed
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
				'name' => 'Coding',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medical Necessity',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Psych',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Readmission',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Rehab',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'No Authorization',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Technical Denial',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Experimental',
				'multiple_service_dates' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Other',
				'multiple_service_dates' => false
			],
		];

		$table = $this->table('denial_types');
		$table->insert($data)->save();
	}
}
