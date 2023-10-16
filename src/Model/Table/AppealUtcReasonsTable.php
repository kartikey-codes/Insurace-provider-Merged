<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Appeal Unable To Complete Reasons Model
 */
class AppealUtcReasonsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('appeal_utc_reasons');

		$this->setPrimaryKey([
			'appeal_id',
			'utc_reason_id',
		]);
	}

	/**
	 * Returns a rules checker object that will be used for validating
	 * application integrity.
	 *
	 * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
	 * @return \Cake\ORM\RulesChecker
	 */
	public function buildRules(RulesChecker $rules): RulesChecker
	{
		$rules->add($rules->existsIn(['appeal_id'], 'Appeals'));
		$rules->add($rules->existsIn(['utc_reason_id'], 'UtcReasons'));

		return $rules;
	}
}
