<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Service\StorageServiceInterface;
use App\Service\Storage\AbstractStorageService;
use Cake\Core\Configure;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

/**
 * Azure Blob Storage Service Provider (Storage Factory)
 *
 * Now uses Flysystem v3 and Azure Storage Adapter instead of maintaining
 * a separate adapter like in V2.
 */
class AzureBlobStorageService extends AbstractStorageService implements StorageServiceInterface
{
	/**
	 * @var array
	 */
	private array $config;

	/**
	 * Array of app container names in key -> value like:
	 * [ 'containerNameInCode' => 'configuredNameOfContainer' ]
	 *
	 * @var array
	 */
	private array $containers;

	/**
	 * @var \MicrosoftAzure\Storage\Blob\BlobRestProxy
	 */
	private BlobRestProxy $client;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->config = Configure::readOrFail('Storage.AzureBlobStorage');
		$this->containers = Configure::readOrFail('Storage.containers');
		$connectionString = $this->getConnectionString();
		$this->client = BlobRestProxy::createBlobService($connectionString);
	}

	/**
	 * @inheritDoc
	 */
	public function exists(string $name): bool
	{
		return $this->containerExists($name);
	}

	/**
	 * @inheritDoc
	 */
	public function create(string $name): bool
	{
		return $this->createContainer($name);
	}

	/**
	 * Create a container if it does not exist already
	 *
	 * @param string $name Name of the container
	 * @return bool
	 */
	protected function createContainer(string $name): bool
	{
		$createContainerOptions = new CreateContainerOptions();
		$createContainerOptions->setPublicAccess(PublicAccessType::NONE); // Private

		$this->client->createContainer($name, $createContainerOptions);

		return true;
	}

	/**
	 * Return if a given container exists or not
	 *
	 * @param string $name
	 * @return bool
	 */
	protected function containerExists(string $name): bool
	{
		$containers = $this->client->listContainers()->getContainers();

		foreach ($containers as $container) {
			if ($container->getName() == $name) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build flysystem adapter from configured container names
	 *
	 * @param string $container Name of the container the app uses
	 * @return \League\Flysystem\Filesystem
	 */
	protected function getFilesystem(string $container): Filesystem
	{
		$containerName = $this->containers[$container];
		$adapter = new AzureBlobStorageAdapter($this->client, $containerName);

		return new Filesystem($adapter);
	}

	/**
	 * Get the full connection string for Azure Blob Storage
	 *
	 * @return string
	 */
	private function getConnectionString(): string
	{
		$protocol = $this->config['protocol'];
		$account = $this->config['storageAccount'];
		$accessKey = $this->config['accessKey'];

		// Build first part of connection string
		$connectionString = sprintf(
			'DefaultEndpointsProtocol=%s;AccountName=%s;AccountKey=%s;',
			$protocol,
			$account,
			$accessKey
		);

		// Azure Storage Emulator
		if ($account == 'devstoreaccount1') {
			$connectionString .= sprintf(
				'BlobEndpoint=%s;QueueEndpoint=%s;TableEndpoint=%s;',
				$protocol . '://127.0.0.1:10000/' . $account,
				$protocol . '://127.0.0.1:10001/' . $account,
				$protocol . '://127.0.0.1:10002/' . $account
			);
		}

		// Return full connection string
		return $connectionString;
	}
}
