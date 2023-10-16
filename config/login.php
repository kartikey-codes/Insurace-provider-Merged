<?php

/**
 * Login Configuration
 */
return [
	'Login' => [
		'backgroundImage' => env('LOGIN_BG', 'login_bg3.jpg'),
		'logo' => env('LOGIN_LOGO', 'RevKeep_tag_white_72dpi.png'), // RevKeep_tag_white_72dpi.png
		'lockoutAttempts' => env('LOGIN_LOCKOUT_ATTEMPTS', 10),
		'lockoutDuration' => env('LOGIN_LOCKOUT_DURATION', 10) // Minutes
	]
];
