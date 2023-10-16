<?php

/**
 * Vendor Configuration
 */
return [
	'Vendor' => [
		/**
		 * obtained by running:
		 * select concat('''', table_name, ''',') from information_schema.tables order by table_name;
		 */
		'allTableNames' => [
			'appeal_levels',
			'appeal_not_defendable_reasons',
			'appeal_notes',
			'appeal_reference_numbers',
			'appeal_types',
			'appeals',
			'case_activity',
			'case_denial_reasons',
			'case_outcomes',
			'case_readmissions',
			'case_types',
			'cases',
			'client_employees',
			'clients',
			'days_to_respond_froms',
			'denial_reasons',
			'denial_types',
			'facilities',
			'facility_types',
			'incoming_documents',
			'insurance_provider_appeal_levels',
			'insurance_provider_insurance_types',
			'insurance_providers',
			'insurance_types',
			'not_defendable_reasons',
			'patients',
			'phinxlog',
			'reference_numbers',
			'user_logins',
			'users',
			'withdrawn_reasons',
			'specialties',
			'vendors',
			'vendor_specialties',
		],
		'vendorTableName' => 'vendors',
		'userTableName' => 'users',
		'noAccessTableNames' => [
			//'client_employees', // Possibly an error? Causes exception when viewing case
			'phinxlog',
			'withdrawn_reasons',
			'specialties',
			'vendors',
			'vendor_specialties',
		],
		'readonlyTableNames' => [
			'appeal_types',
			'appeal_levels',
			'appeal_reference_numbers',
			'cases',
			'case_types',
			'case_outcomes',
			'case_denial_reasons',
			'case_readmissions',
			'clients',
			'days_to_respond_froms',
			'denial_types',
			'denial_reasons',
			'facilities',
			'facility_types',
			'incoming_documents',
			'insurance_providers',
			'insurance_provider_appeal_levels',
			'insurance_provider_insurance_types',
			'insurance_types',
			'not_defendable_reasons',
			'patients',
			'reference_numbers',
			'users',
		],
		// vendor writable tables
		// 	'user_logins',
		// 	'case_activity',
		//	'appeal_notes',
		//	'appeals',
		//	'appeal_not_defendable_reasons',
	]
];
