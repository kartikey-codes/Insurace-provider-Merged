<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Users Roles Model
 */
class UsersRolesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('users_roles');

		$this->setPrimaryKey([
			'user_id',
			'role_id',
		]);

		$this->belongsTo('Roles', [
			'foreignKey' => 'role_id',
		]);

		$this->belongsTo('Users', [
			'foreignKey' => 'user_id',
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
		$rules->add($rules->existsIn(['user_id'], 'Users'));
		$rules->add($rules->existsIn(['role_id'], 'Roles'));

		return $rules;
	}
}
