<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Case Types seed.
 */
class CaseTypesSeed extends AbstractSeed
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
				'name' => 'Commercial Payer Audit (CPA)',
				'short_name' => 'CPA',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Recovery Audit Contractor (RAC)',
				'short_name' => 'RAC',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Comprehensive Error Rate Testing (CERT)',
				'short_name' => 'CERT',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Supplemental Medical Review Contractor (SMRC)',
				'short_name' => 'SMRC',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Unified Program Integrity Contractor (UPIC)',
				'short_name' => 'UPIC',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Targeted Probe-and-Educate (TPE)',
				'short_name' => 'TPE',
				'active' => true
			],
			[
				'created' => $date,
				'modified' => $date,
				'name' => 'Medicare Administrative Contractor Audit (MAC)',
				'short_name' => 'MAC',
				'active' => true
			]
		];

		$table = $this->table('case_types');
		$table->insert($data)->save();
	}
}
