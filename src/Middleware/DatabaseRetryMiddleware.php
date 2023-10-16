<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Application;
use Cake\Database\DriverInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\ConnectionInterface;
use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DatabaseRetryMiddleware implements MiddlewareInterface
{
	/**
	 * @var \App\Application
	 */
	private Application $_app;

	/**
	 * @var ConnectionInterface
	 */
	private ConnectionInterface $connection;

	/**
	 * @var DriverInterface
	 */
	private DriverInterface $driver;

	/**
	 * @var string
	 */
	private string $driverName;

	/**
	 * Constructor
	 */
	public function __construct(Application $app)
	{
		$this->_app = $app;
		$this->connection = ConnectionManager::get('default');
		$this->driver = $this->connection->getDriver();
		$this->driverName = get_class($this->driver);
	}

	/**
	 * Log the amount of database retries needed (waking up from paused)
	 *
	 * Connection retrying was merged into CakePHP core.
	 *
	 * @see https://github.com/cakephp/cakephp/issues/14875
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 * @param \Psr\Http\Server\RequestHandlerInterface $handler
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// Calling $handler->handle() delegates control to the *next* middleware
		// In your application's queue.
		$response = $handler->handle($request);

		// Log number of retries if any
		$retries = $this->driver->getConnectRetries();
		if ($retries > 0) {
			Log::write(LOG_INFO, __('Default connection with driver {0} had {1} retries during request.', $this->driverName, $retries), 'tuning');
		}

		return $response;
	}
}
