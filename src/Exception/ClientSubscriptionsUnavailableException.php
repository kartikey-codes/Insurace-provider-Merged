<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class ClientSubscriptionsUnavailableException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'A list of available subscription plans could not be retrieved at this time. Please try again momentarily.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
