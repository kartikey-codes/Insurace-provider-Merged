<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Http\Exception\BadRequestException;

class FileEntityMissingException extends BadRequestException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'Associated entity record is missing.';

	// You can set a default exception code as well.
	protected $_defaultCode = 400;
}
