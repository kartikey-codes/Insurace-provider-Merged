<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * InsuranceTypes seed.
 */
class InsuranceTypesSeed extends AbstractSeed
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
				'name' => 'Auto',
				'use_provider_guidelines' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medicaid',
				'use_provider_guidelines' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medicare',
				'use_provider_guidelines' => false
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Private',
				'use_provider_guidelines' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Workers Comp',
				'use_provider_guidelines' => true
			]
		];

		$table = $this->table('insurance_types');
		$table->insert($data)->save();
	}
}
