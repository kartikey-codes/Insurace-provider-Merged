<?php

/**
 * Subscriptions Configuration
 */
return [
	'Subscriptions' => [
		'enabled' => env('SUBSCRIPTION_ENABLED', true),
		'required' => env('SUBSCRIPTION_REQUIRED', true),
		'driver' => env('SUBSCRIPTION_DRIVER', 'stripe'),
		'licensingEnabled' => env('LICENSING_ENABLED', true),
		'maxLicenses' => 1000,

		/**
		 * Stripe Configuration
		 */
		'Stripe' => [
			'testMode' => env('STRIPE_TEST_MODE', true),
			'publishableKey' => env('STRIPE_PUBLISHABLE_KEY', 'pk_test_51NGzjoSASQ2TLn41ehvRMRkUNVlk6x1qwCgjVDSS6FCxCK1JjOiN4u3904KfHKs2lK5pfwroL2e8RZctBWTZPp6300xlZvx6ee'), // @required
			'secretKey' => env('STRIPE_SECRET_KEY', 'sk_test_51NGzjoSASQ2TLn41ZBzVAm7dNpWO1emdY89eizevkHNgHD3urpooPSq4ie09Q8N1B7vFaml1yM3XtNODvMgLbisJ000gU5gyVz'), // @required
			'subscriptionProductId' => env('STRIPE_SUBSCRIPTION_PRODUCT_ID', '')
		],
	]
];
