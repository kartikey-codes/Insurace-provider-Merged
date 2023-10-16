<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Disciplines seed.
 */
class DisciplinesSeed extends AbstractSeed
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
				'name' => 'Physical Therapy',
				'short_name' => 'PT'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Occupational Therapy',
				'short_name' => 'OT'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Speech Therapy',
				'short_name' => 'ST'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Cognitive Behavioral Therapy',
				'short_name' => 'CBT'
			],
		];

		$table = $this->table('disciplines');
		$table->insert($data)->save();
	}
}
