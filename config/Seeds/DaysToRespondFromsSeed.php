<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * DaysToRespondFroms seed.
 */
class DaysToRespondFromsSeed extends AbstractSeed
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
				'name' => 'Received Date'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Letter Date'
			]
		];

		$table = $this->table('days_to_respond_froms');
		$table->insert($data)->save();
	}
}
