<?php

declare(strict_types=1);

namespace App\Policy;

use App\Policy\Request\AreaPolicy;
use App\Policy\Request\AuthControllerPolicy;
use App\Policy\Request\DebugKitPolicy;
use App\Policy\Request\UserPermissionPolicy;
use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;

class RequestPolicy implements RequestPolicyInterface
{
	/**
	 * @var array
	 */
	protected array $policies = [];

	/**
	 * @var array
	 */
	protected array $results = [];

	/**
	 * Constructor to register sub-policies
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this
			// App checks
			->addPolicy(new AuthControllerPolicy())
			->addPolicy(new DebugKitPolicy())
			->addPolicy(new AreaPolicy())
			// Finally check permissions if nothing approved the request already
			->addPolicy(new UserPermissionPolicy());
	}

	/**
	 * Method to check if the request can be accessed
	 *
	 * @param \Authorization\IdentityInterface|null Identity
	 * @param \Cake\Http\ServerRequest $request Server Request
	 * @return ?bool
	 */
	public function canAccess(?IdentityInterface $identity, ServerRequest $request): ?bool
	{
		$result = $this->evaluatePolicies($identity, $request);

		// Default to null so auth redirect works
		return is_bool($result) ? $result : null;
	}

	/**
	 * Register a sub-policy to evaluate
	 *
	 * @param \Authorization\Policy\RequestPolicyInterface
	 * @return self
	 */
	private function addPolicy(RequestPolicyInterface $policy): self
	{
		$this->policies[] = $policy;

		return $this;
	}

	/**
	 * Iterate policies and check for results
	 *
	 * @return ?bool
	 */
	private function evaluatePolicies(?IdentityInterface $identity, ServerRequest $request): ?bool
	{
		$this->results = [];

		// Check our sub-policies in order
		foreach ($this->policies as $policy) {
			$result = $this->evaluatePolicy($policy, $identity, $request);

			$this->results[] = [
				'className' => get_class($policy),
				'result' => $result,
			];

			// Null/void will continue to the next policy
			if (is_bool($result) && $result !== null) {
				return $result;
			}
		}

		return null;
	}

	/**
	 * Register a sub-policy to evaluate
	 *
	 * @param \Authorization\Policy\RequestPolicyInterface
	 * @param \Authorization\IdentityInterface|null Identity
	 * @param \Cake\Http\ServerRequest $request Server Request
	 * @return ?bool
	 */
	private function evaluatePolicy(RequestPolicyInterface $policy, ?IdentityInterface $identity, ServerRequest $request): ?bool
	{
		return $policy->canAccess($identity, $request);
	}
}
