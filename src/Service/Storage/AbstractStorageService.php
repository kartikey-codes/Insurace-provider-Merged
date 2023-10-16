<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Service\StorageServiceInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;

/**
 * Base class for storage services to inherit from
 */
abstract class AbstractStorageService implements StorageServiceInterface
{
	/**
	 * @inheritDoc
	 */
	public function exists(string $name): bool
	{
		// Implement in concrete class
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function create(string $name): bool
	{
		// Implement in concrete class
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function admin(): Filesystem
	{
		return $this->getFilesystem('admin');
	}

	/**
	 * @inheritDoc
	 */
	public function appeals(): Filesystem
	{
		return $this->getFilesystem('appeals');
	}

	/**
	 * @inheritDoc
	 */
	public function appealPackets(): Filesystem
	{
		return $this->getFilesystem('appealPackets');
	}

	/**
	 * @inheritDoc
	 */
	public function cases(): Filesystem
	{
		return $this->getFilesystem('cases');
	}

	/**
	 * @inheritDoc
	 */
	public function client(): Filesystem
	{
		return $this->getFilesystem('client');
	}

	/**
	 * @inheritDoc
	 */
	public function guidelines(): Filesystem
	{
		return $this->getFilesystem('guidelines');
	}

	/**
	 * @inheritDoc
	 */
	public function incomingDocuments(): Filesystem
	{
		return $this->getFilesystem('incomingDocuments');
	}

	/**
	 * @inheritDoc
	 */
	public function library(): Filesystem
	{
		return $this->getFilesystem('library');
	}

	/**
	 * @inheritDoc
	 */
	public function logos(): Filesystem
	{
		return $this->getFilesystem('logos');
	}

	/**
	 * @inheritDoc
	 */
	public function patients(): Filesystem
	{
		return $this->getFilesystem('patients');
	}

	/**
	 * @inheritDoc
	 */
	public function vendor(): Filesystem
	{
		return $this->getFilesystem('vendor');
	}

	/**
	 * Build flysystem adapter
	 */
	protected function getFilesystem(string $container): Filesystem
	{
		// Override in concrete class
		$adapter = new InMemoryFilesystemAdapter();
		return new Filesystem($adapter);
	}
}
