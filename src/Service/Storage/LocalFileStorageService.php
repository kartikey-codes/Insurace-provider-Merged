<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Service\StorageServiceInterface;
use App\Service\Storage\AbstractStorageService;
use Cake\Core\Configure;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Local File Storage Service Provider (Storage Factory)
 */
class LocalFileStorageService extends AbstractStorageService implements StorageServiceInterface
{
	/**
	 * The local file system adapter's configuration
	 * i.e. [ 'rootDirectory' => 'storage' ]
	 *
	 * @var array
	 */
	protected array $config;

	/**
	 * Array of app container names in key -> value like:
	 * [ 'containerNameInCode' => 'configuredNameOfContainer' ]
	 *
	 * @var array
	 */
	private array $containers;

	/**
	 * Directory separator character for the host OS
	 *
	 * @var string
	 */
	private string $dirSeparator;

	/**
	 * The name of the directory local files are stored in.
	 * Relative to the application root directory, above webroot.
	 * Default: storage
	 *
	 * @var string
	 */
	private string $storageDirectory;

	/**
	 * The full path to the local file storage directory.
	 * i.e. /var/www/html/storage/
	 *
	 * @var string
	 */
	private string $rootPath;

	/**
	 * Filesystem for the root folder sub-directories are in.
	 * Used for checking if a directory exists or not.
	 *
	 * @var \League\Flysystem\Filesystem
	 */
	private Filesystem $rootFs;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->config = Configure::readOrFail('Storage.Local');
		$this->containers = Configure::readOrFail('Storage.containers');
		$this->dirSeparator = Configure::readOrFail('Storage.directorySeparator');
		$this->storageDirectory = Configure::readOrFail('Storage.Local.rootDirectory');
		$this->rootPath = ROOT . $this->dirSeparator . $this->storageDirectory;

		// Create a Filesystem at the storage root
		$rootAdapter = new LocalFilesystemAdapter($this->rootPath . $this->dirSeparator);
		$this->rootFs = new Filesystem($rootAdapter);
	}

	/**
	 * @inheritDoc
	 */
	public function exists(string $name): bool
	{
		return $this->directoryExists($name);
	}

	/**
	 * @inheritDoc
	 */
	public function create(string $name): bool
	{
		return $this->createDirectory($name);
	}

	/**
	 * Create a directory if it does not exist already
	 *
	 * @param string $name Name of the directory
	 * @return bool
	 */
	protected function createDirectory(string $name): bool
	{
		$this->rootFs->createDirectory($name);

		return true;
	}

	/**
	 * Return if a given directory exists or not
	 *
	 * @param string $name
	 * @return bool
	 */
	protected function directoryExists(string $name): bool
	{
		return $this->rootFs->directoryExists($name);
	}

	/**
	 * Build flysystem adapter
	 *
	 * Maps full path from application source to storage directory
	 * from app configuration / env.
	 * i.e. /storage/<$container>
	 *
	 * Generally used for local development or for a container to
	 * mount a volume.
	 *
	 * Can be used to change names of used storage containers,
	 * i.e. guidelines => 'guidelines-dev-demo-beta-1' or something.
	 */
	protected function getFilesystem(string $container): Filesystem
	{
		$containerName = $this->containers[$container];
		$containerDirectory = $this->rootPath . $this->dirSeparator . $containerName;
		$adapter = new LocalFilesystemAdapter($containerDirectory);

		return new Filesystem($adapter);
	}
}
