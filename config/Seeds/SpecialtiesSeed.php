<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Specialties seed.
 */
class SpecialtiesSeed extends AbstractSeed
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
				'name' => 'Specialty 1',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Specialty 2',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Specialty 3',
			]
		];

		$table = $this->table('specialties');
		$table->insert($data)->save();
	}
}
