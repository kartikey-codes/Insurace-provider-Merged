<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class ClientSubscriptionUpdateException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'Your subscription update could not be processed.';

	// You can set a default exception code as well.
	protected $_defaultCode = 402;
}
