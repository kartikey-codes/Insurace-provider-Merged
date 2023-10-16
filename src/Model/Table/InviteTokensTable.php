<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\InviteToken;
use ArrayObject;
use Cake\Event\EventInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Invite Tokens Model
 */
class InviteTokensTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);

		$this->setTable('invite_tokens');

		$this->setPrimaryKey('id');

		$this->setDisplayField('token');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Clients');

		$this->belongsTo('Vendors');

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Active
		$this->addBehavior('Active', [
			'field' => $this->aliasField('active'),
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [],
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

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// User Auditing
		$this->addBehavior('UserAudit');
	}

	/**
	 * Logic to execute before saving
	 */
	public function beforeSave(EventInterface $event, InviteToken $entity, ArrayObject $options): void
	{
		// Generate API token
		if ($entity->isNew() || empty($entity->token)) {
			$entity->set('token', $entity->generateToken());
		}
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
			->add('active', 'valid', ['rule' => 'boolean'])
			//->requirePresence('active', 'create')
			->allowEmptyString('active');

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
	 * Create a new invite token for a client
	 *
	 * @param int $clientId
	 * @return \App\Model\Entity\InviteToken
	 */
	public function generateForClient(int $clientId): InviteToken
	{
		/**
		 * @var \App\Model\Entity\InviteToken $entity
		 */
		$entity = $this->newEntity([
			'active' => true,
			'client_id' => $clientId,
		]);

		$entity->setToken();
		$this->saveOrFail($entity);

		// Return with all associations populated
		$token = $this->getByTokenValue($entity->token);

		return $token;
	}

	/**
	 * Finder for token
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findByTokenValue(Query $query, array $options): Query
	{
		return $query
			->find('active')
			->where([
				'token' => $options['token'],
			]);
	}

	/**
	 * Determine if a token exists and is active (usable)
	 *
	 * @param string $tokenValue
	 * @return bool
	 */
	public function isUsable(string $tokenValue): bool
	{
		try {
			$token = $this->getByTokenValue($tokenValue);

			return $token->active;
		} catch (PersistenceFailedException $e) {
			return false;
		}
	}

	/**
	 * Get token record and associations based on token value
	 *
	 * @param string $value
	 * @return \App\Model\Entity\InviteToken
	 */
	public function getByTokenValue(string $value): InviteToken
	{
		if ($this->Clients->hasBehavior('Multitenancy')) {
			$this->Clients->removeBehavior('Multitenancy');
		}

		return $this->find('all', [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		])
			->where([
				'token' => $value,
			])
			->select([
				'id',
				'token',
				'active',
			])
			->contain([
				'Clients' => [
					'fields' => [
						'id',
						'name',
					],
				],
				'Vendors' => [
					'fields' => [
						'id',
						'name',
					],
				],
				'CreatedByUser' => [
					'fields' => [
						'first_name',
						'last_name',
						'email',
					],
				],
			])
			->firstOrFail();
	}
}
