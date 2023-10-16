<?php

declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * Insurance Provider Appeal Levels Model
 */
class InsuranceProviderAppealLevelsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('insurance_provider_appeal_levels');

		$this->setPrimaryKey([
			'insurance_provider_id',
			'appeal_level_id',
		]);

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		// Belongs To
		$this->belongsTo('Agencies', [
			'className' => 'Agencies',
			'foreignKey' => 'agency_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */

		// User Auditing
		$this->addBehavior('UserAudit');
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
		$rules->add($rules->existsIn(['insurance_provider_id'], 'InsuranceProviders'));
		$rules->add($rules->existsIn(['appeal_level_id'], 'AppealLevels'));
		$rules->add($rules->existsIn(['agency_id'], 'Agencies'));

		return $rules;
	}
}
