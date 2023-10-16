<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * CaseOutcomes seed.
 */
class CaseOutcomesSeed extends AbstractSeed
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
				'name' => 'Favorable'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Unfavorable'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Withdrawn'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Unknown'
			]
		];

		$table = $this->table('case_outcomes');
		$table->insert($data)->save();
	}
}
