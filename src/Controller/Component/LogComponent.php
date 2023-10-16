<?php

declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\Log\Log;
use ReflectionClass;
use Throwable;

/**
 * Logging Component
 *
 * @todo Encapsulate logging events into an object
 * @property \App\Controller\Component\Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class LogComponent extends Component
{
	/**
	 * Default config
	 *
	 * These are merged with user-provided config when the component is used.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [
		'user' => [
			'nameField' => 'email',
			'primaryKey' => 'id',
		],
	];

	/**
	 * Other components that this component relies on
	 *
	 * @var array
	 */
	protected $components = [
		'Authentication',
	];

	/**
	 * Log scope for ORM related actions
	 *
	 * @todo Move to configuration value
	 * @var string
	 */
	public $logName = 'orm';

	/**
	 * Initialization
	 *
	 * @param array $config Array of config options.
	 */
	public function initialize(array $config): void
	{
		// Call Cake component initialization
		parent::initialize($config);

		// Levels:
		// Emergency: system is unusable
		// Alert: action must be taken immediately
		// Critical: critical conditions
		// Error: error conditions
		// Warning: warning conditions
		// Notice: normal but significant condition
		// Info: informational messages
		// Debug: debug-level messages
	}

	/**
	 * Entity Save Failed
	 *
	 * Handles logging a persistence failed exception.
	 *
	 * @param \Throwable $e
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @return bool
	 */
	public function saveFailed(Throwable $e, EntityInterface $entity): bool
	{
		$message = __(
			'Failed to save [{0}] with [{1}]: {2}',
			(new ReflectionClass($entity))->getShortName(),
			(new ReflectionClass($e))->getShortName(),
			$e->getMessage()
		);

		$message = $this->appendEntityErrors($message, $entity);
		$message = $this->appendUser($message);
		$message = $this->appendRequestData($message);

		return Log::write('warning', $message, $this->logName);
	}

	/**
	 * Entity Delete Failed
	 *
	 * Handles logging when an entity fails to delete.
	 *
	 * @param \Throwable $e
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @return bool
	 */
	public function deleteFailed(Throwable $e, EntityInterface $entity): bool
	{
		$message = __(
			'Failed to delete [{0}] with [{1}]: {2}',
			(new ReflectionClass($entity))->getShortName(),
			(new ReflectionClass($e))->getShortName(),
			$e->getMessage()
		);

		$message = $this->appendEntityData($message, $entity);
		$message = $this->appendEntityErrors($message, $entity);
		$message = $this->appendUser($message);
		$message = $this->appendRequestData($message);

		return Log::write('error', $message, $this->logName);
	}

	/**
	 * Entity Delete Succeeded
	 *
	 * Handles logging when an entity is deleted
	 *
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @return bool
	 */
	public function deleteSuccess(EntityInterface $entity): bool
	{
		$message = __(
			'Deleted [{0}]',
			(new ReflectionClass($entity))->getShortName()
		);

		$message = $this->appendEntityData($message, $entity);
		$message = $this->appendEntityErrors($message, $entity);
		$message = $this->appendUser($message);
		$message = $this->appendRequestData($message);

		return Log::write('notice', $message, $this->logName);
	}

	/**
	 * Append Entity Data
	 *
	 * Appends related entity data to log message.
	 *
	 * @param string $message
	 * @param ?\Cake\Datasource\EntityInterface $entity
	 * @return string
	 */
	protected function appendEntityData(string $message, ?EntityInterface $entity): string
	{
		// Append Errors
		if (!empty($entity)) {
			$message .= PHP_EOL . __('Entity: {0}', print_r($entity->toArray(), true));
		}

		return $message;
	}

	/**
	 * Append Entity Errors
	 *
	 * Appends related entity error information to log message.
	 *
	 * @param string $message
	 * @param ?\Cake\Datasource\EntityInterface $entity
	 * @return string
	 */
	protected function appendEntityErrors(string $message, ?EntityInterface $entity): string
	{
		// Append Errors
		if (!empty($entity) && !empty($entity->getErrors())) {
			$message .= PHP_EOL . __('Errors: {0}', print_r($entity->getErrors(), true));
		}

		return $message;
	}

	/**
	 * Append User
	 *
	 * Appends current user information to log message.
	 *
	 * @param string $message
	 * @param array $user
	 * @return string $message
	 */
	protected function appendUser(string $message, array $user = []): string
	{
		// Get user
		if (empty($user) && !empty($this->Authentication->getIdentity())) {
			$user = $this->Authentication->getIdentity()->getOriginalData();
		}

		// Fields
		$nameField = $this->getConfig('user.nameField');
		$primaryKey = $this->getConfig('user.primaryKey');

		// Append User
		if (!empty($user)) {
			$message .= PHP_EOL . __('User: {0} (#{1})', $user[$nameField], $user[$primaryKey]);
		}

		return $message;
	}

	/**
	 * Append Metadata
	 *
	 * Appends request info to the message
	 *
	 * @param string $message
	 * @return string $message
	 */
	protected function appendRequestData(string $message): string
	{
		// Get request from the controller using this component
		$request = $this->getController()->getRequest();

		// Append URL
		if (!empty($request->getRequestTarget())) {
			$message .= PHP_EOL . __('URL: {0}', $request->getRequestTarget());
		}

		// Append Request Data
		if (!empty($request->getData())) {
			$message .= PHP_EOL . __('Request Data: {0}', print_r($request->getData(), true));
		}

		return $message;
	}
}
