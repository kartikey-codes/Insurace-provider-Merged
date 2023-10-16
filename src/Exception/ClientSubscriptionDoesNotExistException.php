<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class ClientSubscriptionDoesNotExistException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'The client\'s subscription ID could not be found by the payment service provider.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
