<?php

declare(strict_types=1);

namespace App\Lib\AppHealthUtility;

/**
 * Class for application health checks
 */
class AppHealthUtility
{
	/**
	 * Determine if the application is in a healthy state
	 * and all runtime requirements have been met.
	 *
	 * This function would be called every 10 minutes or so
	 * by Docker to run health checks, so don't include anything
	 * that shouldn't be executed frequently.
	 *
	 * @return bool
	 */
	public static function isHealthy(): bool
	{
		/**
		 * @todo Check if database is connected
		 * @todo Check if storage location is accessible
		 */

		return true;
	}
}
