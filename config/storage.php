<?php

/**
 * File Storage Configuration
 */
return [
	'Storage' => [
		'checkDb' => env('STORAGE_CHECK_DATABASE', true),
		'containers' => [
			'admin' => env('STORAGE_ADMIN', 'admin'),
			'appeals' => env('STORAGE_APPEALS', 'appeals'),
			'appealPackets' => env('STORAGE_APPEAL_PACKETS', 'appeal-packets'),
			'cases' => env('STORAGE_CASES', 'cases'),
			'client' => env('STORAGE_CLIENT', 'client'),
			'guidelines' => env('STORAGE_GUIDELINES', 'guidelines'),
			'incomingDocuments' => env('STORAGE_INCOMING_DOCUMENTS', 'incoming-documents'),
			'library' => env('STORAGE_LIBRARY', 'library'),
			'logos' => env('STORAGE_LOGOS', 'logos'),
			'patients' => env('STORAGE_PATIENTS', 'patients'),
			'vendor' => env('STORAGE_VENDOR', 'vendor'),
		],
		'directorySeparator' => env('STORAGE_DS', '/'),
		'driver' => env('STORAGE_DRIVER', 'local'),

		/**
		 * Azure Blob Storage Configuration
		 */
		'AzureBlobStorage' => [
			// Azure storage emulator default shared access key
			'storageAccount' => env('AZURE_STORAGE_ACCOUNT', 'devstoreaccount1'),
			'accessKey' => env('AZURE_STORAGE_ACCESS_KEY', 'Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw=='),
			'protocol' => env('AZURE_STORAGE_PROTOCOL', 'http')
		],

		/**
		 * Local Files Configuration
		 */
		'Local' => [
			// Relative to application root directory (/)
			'rootDirectory' => env('STORAGE_LOCAL_DIRECTORY', 'storage')
		]
	]
];
