<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Appeal Levels seed.
 */
class AppealLevelsSeed extends AbstractSeed
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
				'name' => 'Level 1',
				'short_name' => 'Level 1',
				'description' => '1st Level Appeal',
				'active' => true,
				'order_number' => 1
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Level 2',
				'short_name' => 'Level 2',
				'description' => '2nd Level Appeal',
				'active' => true,
				'order_number' => 2
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Level 3',
				'short_name' => 'Level 3',
				'description' => '3rd Level Appeal',
				'active' => true,
				'order_number' => 3
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Level 4',
				'short_name' => 'Level 4',
				'description' => '4th Level Appeal',
				'active' => true,
				'order_number' => 4
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Level 5',
				'short_name' => 'Level 5',
				'description' => '5th Level Appeal',
				'active' => true,
				'order_number' => 5
			]
		];

		$table = $this->table('appeal_levels');
		$table->insert($data)->save();
	}
}
