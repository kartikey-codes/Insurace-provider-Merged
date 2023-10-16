<?php

declare(strict_types=1);

/**
 * File Storage Prefix Component
 *
 * This component handles ensuring file storage path prefixes
 * that use entity IDs exist and are available to the tenant.
 *
 * E.x. Ensures client uploading file under 1/myfile.pdf has
 * a matching entity with ID 1 belonging to them.
 */

namespace App\Controller\Component;

use App\Exception\FileEntityMissingException;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Exception;

class EntityFilesComponent extends Component
{
	use LocatorAwareTrait;

	/**
	 * Store the entity ID that we're working with
     *
     * @var int
	 */
	private int $entityId;

	/**
	 * Associated table for checking entity against
     *
     * @var string
	 */
	private string $table;

	/**
	 * File path prefix
     *
     * @var string
	 */
	private string $pathPrefix = '';

	/**
	 * Directory separator character to use
     *
     * @var string
	 */
	private string $directorySeparator = '/';

	/**
	 * Option to check if the entity exists in the database
     *
     * @var bool
	 */
	private bool $checkDatabase = true;

	/**
	 * Whether to bypass tenant checking when
	 * validating entity exists
     *
     * @var bool
	 */
	private bool $isAdmin = false;

	/**
	 * Constructor hook method.
	 *
	 * Implement this method to avoid having to overwrite
	 * the constructor and call parent.
	 *
	 * @param array<string, mixed> $config The configuration settings provided to this component.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		if (empty($config['table'])) {
			throw new Exception(__('Table name is required for File Storage Prefix component.'));
		}

		if (empty($config['id'])) {
			throw new Exception(__('An entity primary key is required for file storage prefixing.'));
		}

		$this->table = $config['table'];
		$this->isAdmin = $config['isAdmin'] ?? false;
		$this->entityId = intval($config['id']);
		$this->pathPrefix = strval($config['id']);
		$this->directorySeparator = Configure::readOrFail('Storage.directorySeparator');

		// Config for checking IDs against database
		$this->checkDatabase = (bool)Configure::read('Storage.checkDb', true);

		// Allow checking if the record exists in the database
		if ($this->checkDatabase) {
			$this->ensureExists();
		}
	}

	/**
	 * Check if the entity ID we're using is valid
	 *
	 * @return bool
	 * @throws \App\Exception\FileEntityMissingException
	 */
	private function ensureExists(): bool
	{
		try {
			// Load model's table class
			$table = $this->fetchTable($this->table);

			// Attempt to ensure model record exists
			// Admin users can skip tenancy check
			$table->get($this->entityId, [
				'skipTenantCheck' => $this->isAdmin,
			]);

			return true;
		} catch (RecordNotFoundException) {
			throw new FileEntityMissingException(__(
				'Associated record in {0} is missing.',
				$this->table
			));
		}
	}

	/**
	 * Get file path prefix, which is just a string and
	 * a directory separator.
     *
     * @return string
	 */
	public function getPrefix(): string
	{
		return $this->pathPrefix . $this->directorySeparator;
	}

	/**
	 * Prefix file name path to keep files under a subdirectory (based on entity ID)
     *
     * @param string $fileName
	 * @return string
	 */
	public function prefixPath(?string $fileName = null): string
	{
		if (empty($fileName)) {
			return $this->getPrefix();
		}

		return $this->getPrefix() . $fileName;
	}
}
