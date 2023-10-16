<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Appeal Types seed.
 */
class AppealTypesSeed extends AbstractSeed
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
				'name' => 'Pre-Payment',
				'short_name' => 'PRE',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Post-Payment',
				'short_name' => 'POST',
				'active' => true
			]
		];

		$table = $this->table('appeal_types');
		$table->insert($data)->save();
	}
}
