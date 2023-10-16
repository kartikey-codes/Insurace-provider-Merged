<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Roles Permissions Model
 */
class RolesPermissionsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('roles_permissions');

		$this->setPrimaryKey([
			'role_id',
			'permission_id',
		]);

		$this->belongsTo('Roles', [
			'foreignKey' => 'role_id',
		]);

		$this->belongsTo('Permissions', [
			'foreignKey' => 'permission_id',
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
		$rules->add($rules->existsIn(['role_id'], 'Roles'));
		$rules->add($rules->existsIn(['permission_id'], 'Permissions'));

		return $rules;
	}
}
