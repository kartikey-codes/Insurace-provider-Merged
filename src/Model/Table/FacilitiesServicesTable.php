<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Facilities Services Model
 */
class FacilitiesServicesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('facilities_services');

		$this->setPrimaryKey([
			'facility_id',
			'service_id',
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
		$rules->add($rules->existsIn(['facility_id'], 'Facilities'));
		$rules->add($rules->existsIn(['service_id'], 'Services'));

		return $rules;
	}
}
