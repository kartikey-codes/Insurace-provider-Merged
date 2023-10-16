<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * Users Logins Model
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UserLoginsTable extends Table
{
	/**
	 * The field on the user model used in the login form
	 *
	 * @var string
	 */
	public string $usernameField = 'email';

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		$this->setTable('user_logins');

		$this->setPrimaryKey('id');

		$this->setDisplayField('created');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

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

		return $rules;
	}

	/**
	 * Find Successful logins
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findSuccess(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp) {
			return $exp->eq('success', true);
		});
	}

	/**
	 * Find failed login attempts
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findFailed(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp) {
			return $exp->eq('success', false);
		});
	}

	/**
	 * Find just log-ins
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findLogin(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp) {
			return $exp->eq('logout', false);
		});
	}

	/**
	 * Find just log-outs
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findLogout(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp) {
			return $exp->eq('logout', true);
		});
	}

	/**
	 * Records a successful login
	 *
	 * @param \App\Model\Entity\User $user
	 * @param \Cake\Event\Event $event
	 * @param array $options
	 * @return \Cake\Datasource\EntityInterface
	 */
	public function success(User $user, Event $event, array $options = []): EntityInterface
	{
		$event->stopPropagation();

		$request = $event->getSubject()->getRequest();

		$entity = $this->newEntity([
			'user_id' => $user->id,
			'username' => $user->{$this->usernameField},
			'success' => true,
			'logout' => false,
			'ip_address' => $request->clientIp(),
			'hostname' => gethostbyaddr($request->clientIp()),
			'user_agent' => $request->getHeaderLine('User-Agent'),
			'created' => new FrozenTime(),
		]);

		return $this->saveOrFail($entity);
	}

	/**
	 * Records a failed login
	 *
	 * @param array $formData
	 * @param \Cake\Event\Event $event
	 * @param array $options
	 * @return \Cake\Datasource\EntityInterface
	 */
	public function failure(array $formData, Event $event, array $options = []): EntityInterface
	{
		$event->stopPropagation();

		$request = $event->getSubject()->getRequest();

		// Look up user ID from username
		$user = $this->Users
			->find('all', [
				'skipTenantCheck' => true,
			])
			->select([
				'id',
				$this->usernameField,
			])
			->where([
				$this->usernameField => $formData[$this->usernameField],
			])
			->first();

		// Save user ID if the username matches a user
		if (!empty($user)) {
			$userId = $user->id;
		} else {
			$userId = null;
		}

		$entity = $this->newEntity([
			'user_id' => $userId,
			'username' => $formData[$this->usernameField],
			'success' => false,
			'logout' => false,
			'ip_address' => $request->clientIp(),
			'hostname' => gethostbyaddr($request->clientIp()),
			'user_agent' => $request->getHeaderLine('User-Agent'),
			'created' => new FrozenTime(),
		]);

		$saved = $this->saveOrFail($entity);

		if (!empty($user)) {
			$this->Users->checkLockout($user->id);
		}

		return $saved;
	}

	/**
	 * Records a successful logout
	 *
	 * @param \App\Model\Entity\User $user
	 * @param \Cake\Event\Event $event
	 * @param array $options
	 * @return ?\Cake\Datasource\EntityInterface
	 */
	public function logout(User $user, EventInterface $event, array $options = []): ?EntityInterface
	{
		$event->stopPropagation();

		$request = $event->getSubject()->getRequest();

		if (!empty($options['details'])) {
			$details = $options['details'];
		} else {
			$details = null;
		}

		// Disable multitenancy behavior
		if ($this->Users->hasBehavior('Multitenancy')) {
			$this->Users->removeBehavior('Multitenancy');
		}

		// Skip if the user was deleted (like during dev)
		try {
			if (!$this->Users->exists(['id' => $user->id])) {
				return null;
			}
		} catch (RecordNotFoundException $e) {
			return null;
		}

		$entity = $this->newEntity([
			'user_id' => $user->id,
			'username' => $user->{$this->usernameField},
			'success' => true,
			'logout' => true,
			'details' => $details,
			'ip_address' => $request->clientIp(),
			'hostname' => gethostbyaddr($request->clientIp()),
			'user_agent' => $request->getHeaderLine('User-Agent'),
			'created' => new FrozenTime(),
		], [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		return $this->save($entity, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
			'associated' => [],
		]);
	}
}
