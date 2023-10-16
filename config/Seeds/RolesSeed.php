<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Roles seed.
 */
class RolesSeed extends AbstractSeed
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
				'name' => 'Administrative'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Management'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Physician'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Case Manager'
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Billing'
			]
		];

		$table = $this->table('roles');
		$table->insert($data)->save();
	}
}
