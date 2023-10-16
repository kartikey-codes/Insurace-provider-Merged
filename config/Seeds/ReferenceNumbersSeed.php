<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Reference Numbers seed.
 */
class ReferenceNumbersSeed extends AbstractSeed
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
				'name' => 'Appeal Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'ALJ Appeal Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Claim Reference',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Documentation Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'HIC Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medical Record Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medicare Appeal Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medicare Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Provider Number',
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Provider Tax ID',
			]
		];

		$table = $this->table('reference_numbers');
		$table->insert($data)->save();
	}
}
