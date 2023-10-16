<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\AppealNoteListener;
use App\Model\Filter\AppealNotesCollection;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Appeal Notes Model
 */
class AppealNotesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('appeal_notes');

		$this->setPrimaryKey('id');

		$this->setDisplayField('notes');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Appeals', [
			'foreignKey' => 'appeal_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// User Auditing
		$this->addBehavior('UserAudit');

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'CreatedByUser',
				'ModifiedByUser',
			],
		]);

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
				$this->aliasField('created') => 'DESC',
			],
		]);

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => AppealNotesCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new AppealNoteListener()
		);
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
			->add('appeal_id', 'valid', ['rule' => 'numeric'])
			->notEmptyString('appeal_id', 'create');

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
		$rules->add($rules->existsIn(['appeal_id'], 'Appeals'));

		return $rules;
	}

	/**
	 * After Save
	 *
	 * @return void
	 */
	public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($entity->isNew()) {
			$event = new Event('Model.AppealNote.created', $this, compact('entity'));
			$this->getEventManager()->dispatch($event);
		}
	}

	/**
	 * Finder for appeals (ordered and with associations)
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findOrderedAndFull(Query $query, array $options): Query
	{
		/**
		 * @todo Debug admin user not showing up as CreatedByUser/ModifiedByUser
		 * here. Seems to be something with tenant check.
		 */

		return $query->find('ordered')->find('full');
	}
}
