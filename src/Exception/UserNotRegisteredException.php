<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

/**
 * User was requested by email address and was not found
 *
 * @package App\Exception
 */
class UserNotRegisteredException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'No account was not found using the provided email address.';

	// You can set a default exception code as well.
	protected $_defaultCode = 400;
}
