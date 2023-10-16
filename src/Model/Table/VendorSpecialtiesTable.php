<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Vendor Specialties Model
 */
class VendorSpecialtiesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('vendor_specialties');

		$this->setPrimaryKey('id');

		$this->setDisplayField('id');

		$this->belongsTo('Vendors', [
			'foreignKey' => 'vendor_id',
		]);

		$this->belongsTo('Specialties', [
			'foreignKey' => 'specialty_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Soft Delete
		/* disable this for now since it doesn't work well with belongsToMany
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted'
		]);
		*/

		// User Auditing
		$this->addBehavior('UserAudit');

		// Timestamp Behavior
		$this->addBehavior('Timestamp', [
			'events' => [
				'Model.beforeSave' => [
					'created' => 'new',
					'modified' => 'always',
				],
			],
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
		$rules->add($rules->existsIn(['vendor_id'], 'Vendors'));
		$rules->add($rules->existsIn(['specialty_id'], 'Specialties'));

		return $rules;
	}
}
