<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Not Defendable Reasons seed.
 */
class NotDefendableReasonsSeed extends AbstractSeed
{
	/**
	 * Run Method
	 * @return void
	 */
	public function run(): void
	{
		$date = date('Y-m-d H:i:s');

		$list = [
			'CMS 2-Midnight Rule not met',
			'Delay in treatment',
			'Attending physician did not intend for Inpatient admission',
			'Admissions should be considered combined',
			'Patient not stable at the time of discharge from first admission',
			'Care could have been completed during the initial admission',
			'Appropriate follow-up care not arranged prior to discharge',
			'Diagnosis sequencing',
			'Medical documentation supplied did not support the assignment of billed Procedure Code',
			'Medical record does not support principal diagnosis',
			'Medical record does not support secondary diagnosis'
		];

		$data = [];
		foreach ($list as $listItem) {
			$data[] = [
				'created' => $date,
				'modified' => $date,
				'name' => $listItem
			];
		}

		$this->table('not_defendable_reasons')
			->insert($data)
			->save();
	}
}
