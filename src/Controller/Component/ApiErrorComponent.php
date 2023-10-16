<?php

declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Throwable;

/**
 * API Error Component
 *
 * Handles common ORM exceptions such as failed validation and building a response
 */
class ApiErrorComponent extends Component
{
	/**
	 * Default config
	 *
	 * These are merged with user-provided config when the component is used.
	 *
	 * @var array
	 */
	protected $_defaultConfig = [];

	/**
	 * Components this one relies on
	 */
	protected $components = [];

	/**
	 * Initialization
	 *
	 * @param array $config Array of config options.
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);
	}

	/**
	 * Entity error
	 *
	 * @param \Throwable $e Exception that was thrown
	 * @param \Cake\ORM\Entity $entity The entity that failed
	 * @return \Cake\Http\Response
	 */
	public function entity(Throwable $e, EntityInterface $entity): Response
	{
		// Set error messages
		$this->getController()->set([
			'errors' => $entity->getErrors(),
			'message' => $e->getMessage(),
		]);

		// Return 422 Unprocessible Entity
		return $this->getController()->getResponse()->withStatus(422);
	}

	/**
	 * Error
	 *
	 * @param \Throwable $e Exception that was thrown
	 * @param string $message The error message
	 * @return \Cake\Http\Response
	 */
	public function error(Throwable $e, string $message): Response
	{
		// Set error messages
		$this->getController()->set([
			'errors' => $e->getMessage(),
			'message' => $message,
		]);

		// Return 500 Unprocessible Entity
		return $this->getController()->getResponse()->withStatus(500);
	}
}
