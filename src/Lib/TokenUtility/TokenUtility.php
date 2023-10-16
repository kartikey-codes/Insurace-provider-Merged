<?php

declare(strict_types=1);

namespace App\Lib\TokenUtility;

use Exception;

/**
 * Class for handling generating random tokens like in password
 * resets, invite codes, etc.
 */
class TokenUtility
{
	/**
	 * Generate Password Reset Token
	 *
	 * @param string $salt
	 * @return string
	 */
	public static function passwordReset(?string $salt = null): string
	{
		// Mix in the salt and other ingredients
		$string = $salt . strtotime('now') . rand(1, 100);

		// Return a hash of the supplied string
		// Doesn't have to be cryptographically secure, just kinda random
		return sha1($string);
	}

	/**
	 * Generate a temporary user password for new users
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function temporaryPassword(): string
	{
		return bin2hex(random_bytes(4));
	}

	/**
	 * Generate a random API token for a user to use
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function apiToken(): string
	{
		return strtoupper(bin2hex(random_bytes(30)));
	}
}
