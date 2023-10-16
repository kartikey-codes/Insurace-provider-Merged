<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Case Denial Reasons Model
 */
class CaseDenialReasonsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('case_denial_reasons');

		$this->setPrimaryKey('id');

		$this->setDisplayField('id');

		$this->belongsTo('Cases', [
			'foreignKey' => 'case_id',
		]);

		$this->belongsTo('DenialReasons', [
			'foreignKey' => 'denial_reason_id',
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
		$rules->add($rules->existsIn(['case_id'], 'Cases'));
		$rules->add($rules->existsIn(['denial_reason_id'], 'DenialReasons'));

		return $rules;
	}
}
