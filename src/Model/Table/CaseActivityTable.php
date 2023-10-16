<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Case Activity Model
 */
class CaseActivityTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('case_activity');

		$this->setPrimaryKey([
			'case_id',
			'user_id',
		]);

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Cases', [
			'foreignKey' => 'case_id',
		]);

		$this->belongsTo('Users', [
			'foreignKey' => 'user_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
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
		$rules->add($rules->existsIn(['user_id'], 'Users'));

		return $rules;
	}

	/**
	 * Finder for within timeframe
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findCurrent(Query $query, array $options): Query
	{
		return $query->where(function ($exp) {
			return $exp->gte($this->aliasField('modified'), new FrozenTime('-2 minutes'));
		});
	}

	/**
	 * Update a user's case activity
	 *
	 * @param int $caseId
	 * @param int $userId
	 * @return bool
	 */
	public function touch(int $caseId, int $userId): bool
	{
		$params = [
			'case_id' => $caseId,
			'user_id' => $userId,
		];

		$entity = $this->find()
			->where($params)
			->first();

		if (empty($entity)) {
			$entity = $this->newEntity($params);
		} else {
			$entity->set('modified', new FrozenTime());
		}

		return $this->save($entity) ? true : false;
	}
}
