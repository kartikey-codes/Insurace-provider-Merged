<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Integration;
use App\Model\Filter\IntegrationsCollection;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use InvalidArgumentException;

/**
 * Integrations Model
 *
 */
class IntegrationsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('integrations');

		$this->setPrimaryKey('id');

		$this->setDisplayField('integration_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [],
			'limitedFields' => [
				'id',
				'integration_name',
				'description',
				// Config JSON is hidden
				'last_accessed',
				'expiration_date',
				'enabled',
				'connected'
			],
		]);

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

		// Orderable
		$this->addBehavior('Orderable', [
			'default' => [
				$this->aliasField('integration_name') => 'asc',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => IntegrationsCollection::class,
		]);
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->add('id', 'valid', ['rule' => 'numeric'])
			->allowEmptyString('id', 'create');

		$validator
			->maxLength('integration_name', 100)
			->requirePresence('integration_name', 'create')
			->notEmptyString('integration_name');

		$validator
			->maxLength('description', 100)
			->requirePresence('description', 'create')
			->notEmptyString('description');

		return $validator;
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
		return $rules;
	}

	/**
	 * Finder for matching an appeal level
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByName(Query $query, array $options): Query
	{
		$name = $options['name'];

		if (empty($name)) {
			throw new InvalidArgumentException(__('The integration name must be provided and valid'));
		}

		$values = Integration::getAllNames();
		if (!in_array($name, $values)) {
			throw new InvalidArgumentException(__('The name provided is not a supported integration'));
		}

		return $query->where([
			'integration_name' => $name
		]);
	}
}
