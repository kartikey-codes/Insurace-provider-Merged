<?php

/**
 * SSL Configuration
 */
return [
	'SSL' => [
		// Used in Docker environments with SSL to force ajax requests
		// to use https.
		'upgradeInsecureRequests' => env('SSL_UPGRADE_INSECURE', false)
	]
];
