<?php

declare(strict_types=1);

namespace App\Service;

use League\Flysystem\Filesystem;

/**
 * Storage Service Provider Interface (Storage Factory)
 */
interface StorageServiceInterface
{
	/**
	 * Check if storage location exists
	 *
	 * @param string $name Name of the container or directory
	 * @return bool
	 */
	public function exists(string $name): bool;

	/**
	 * Create storage location
	 *
	 * @param string $name Name of the container or directory
	 * @return bool
	 */
	public function create(string $name): bool;

	/**
	 * Admin Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function admin(): Filesystem;

	/**
	 * Appeal Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function appeals(): Filesystem;

	/**
	 * Appeal Packets
	 */
	public function appealPackets(): Filesystem;

	/**
	 * Case Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function cases(): Filesystem;

	/**
	 * Client Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function client(): Filesystem;

	/**
	 * Guidelines (Insurance Provider Files)
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function guidelines(): Filesystem;

	/**
	 * Incoming Documents
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function incomingDocuments(): Filesystem;

	/**
	 * Library
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function library(): Filesystem;

	/**
	 * Logo Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function logos(): Filesystem;

	/**
	 * Patient Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function patients(): Filesystem;

	/**
	 * Vendor Files
	 *
	 * @return \League\Flysystem\Filesystem
	 */
	public function vendor(): Filesystem;
}
