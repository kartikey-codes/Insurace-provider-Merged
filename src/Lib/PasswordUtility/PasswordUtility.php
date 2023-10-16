<?php

declare(strict_types=1);

namespace App\Lib\PasswordUtility;

use Cake\Core\Configure;

/**
 * Password Utility
 *
 * Used for aiding password validation and ensuring
 * secure passwords are used.
 */
class PasswordUtility
{
	/**
	 * Lower-case list of commonly used passwords
	 * @var array
	 */
	public static array $commonPasswords = [
		'aaaa',
		'bbbb',
		'cccc',
		'dddd',
		'abc123',
		'letmein',
		'password',
		'password1',
		'guest',
		'admin',
		'qwerty',
		'qwertyuiop',
		'iloveyou',
		'123123',
		'1234',
		'12345',
		'123456',
		'1234567',
		'12345678',
		'123456789',
		'1234567890',
		'1111',
		'11111',
		'111111',
		'0000',
		'00000',
		'000000'
	];

	/**
	 * Return the minimum password length from configuration
	 * @return int
	 */
	public static function getMinimumLength(): int
	{
		return Configure::readOrFail('Passwords.minLength');
	}

	/**
	 * Return whether the password is commonly used and
	 * is thus insecure and exploitable
	 * @return bool
	 */
	public static function isCommonlyUsed(string $value): bool
	{
		return in_array(strtolower($value), self::$commonPasswords);
	}
}
