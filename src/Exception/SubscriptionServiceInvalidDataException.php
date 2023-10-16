<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class SubscriptionServiceInvalidDataException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'Subscription service provider requires field `%s`.';

	// You can set a default exception code as well.
	protected $_defaultCode = 400;
}
