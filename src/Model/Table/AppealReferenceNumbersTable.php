<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Appeal Not Defendable Reasons Model
 */
class AppealReferenceNumbersTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('appeal_reference_numbers');

		$this->setPrimaryKey('id');

		$this->setDisplayField('value');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Appeals', [
			'foreignKey' => 'appeal_id',
		]);

		$this->belongsTo('ReferenceNumbers', [
			'foreignKey' => 'reference_number_id',
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
		$rules->add($rules->existsIn(['reference_number_id'], 'ReferenceNumbers'));

		return $rules;
	}
}
