<?php

declare(strict_types=1);

namespace App\Policy\Request;

use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserPermissionPolicy implements RequestPolicyInterface
{
	use LocatorAwareTrait;

	/**
	 * Method to check if the request can be accessed
	 *
	 * @param \Authorization\IdentityInterface|null Identity
	 * @param \Cake\Http\ServerRequest $request Server Request
	 * @return ?bool
	 */
	public function canAccess(?IdentityInterface $identity, ServerRequest $request): ?bool
	{
		/**
		 * @todo Reimplement user permission checking based on something more
		 * maintainable than a database table of every single action in the app.
		 */

		return true;
	}
}
