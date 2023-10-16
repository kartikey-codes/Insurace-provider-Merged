<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Withdrawn Reasons seed.
 */
class WithdrawnReasonsSeed extends AbstractSeed
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
				'name' => 'Unknown'
			]
		];

		$table = $this->table('withdrawn_reasons');
		$table->insert($data)->save();
	}
}
