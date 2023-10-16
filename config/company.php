<?php

/**
 * Company Configuration
 */
return [
	'Company' => [
		'name' => env('COMPANY_NAME', 'RevKeep Software'),
		'nameLegal' => env('COMPANY_NAME_LEGAL', 'RevKeep Software, LLC'),
		'nameShort' => env('COMPANY_NAME_SHORT', 'RK'),
		'address' => [
			'line1' => env('COMPANY_ADDRESS_LINE1', '1221 Bowers St.'),
			'line2' => env('COMPANY_ADDRESS_LINE2', '#2624'),
			'city' => env('COMPANY_ADDRESS_CITY', 'Birmingham'),
			'state' => env('COMPANY_ADDRESS_STATE', 'Michigan'),
			'stateCode' => env('COMPANY_ADDRESS_STATE_CODE', 'MI'),
			'zipCode' => env('COMPANY_ADDRESS_ZIP_CODE', '48012'),
		],
		'phoneNumber' => env('COMPANY_PHONE_NUMBER', '2482152975'),
		'faxNumber' => env('COMPANY_FAX_NUMBER', '8777864977'),
		'publicEmail' => env('COMPANY_PUBLIC_EMAIL', 'info@revkeepsoftware.com'),
		'website' => env('COMPANY_WEBSITE', 'https://revkeepsoftware.com')
	]
];
