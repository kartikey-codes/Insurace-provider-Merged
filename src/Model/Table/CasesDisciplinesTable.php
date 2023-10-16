<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Cases Disciplines Model
 */
class CasesDisciplinesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('cases_disciplines');

		$this->setPrimaryKey([
			'case_id',
			'discipline_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */

		// None
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
		$rules->add($rules->existsIn(['case_id'], 'Cases'));
		$rules->add($rules->existsIn(['discipline_id'], 'Disciplines'));

		return $rules;
	}
}
