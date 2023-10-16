<?php

/**
 * Multi-tenancy Configuration
 */
return [
	'Multitenancy' => [
		/**
		 * obtained by running:
		 * select concat('''', table_name, ''',') from information_schema.tables order by table_name;
		 */
		'allTableNames' => [
			'agencies',
			'agency_outgoing_profile',
			'appeal_levels',
			'appeal_not_defendable_reasons',
			'appeal_notes',
			'appeal_reference_numbers',
			'appeal_types',
			'appeal_utc_reasons',
			'appeals',
			'audit_reviewers',
			'case_activity',
			'case_denial_reasons',
			'case_outcomes',
			'case_readmissions',
			'case_requests',
			'case_types',
			'cases',
			'cases_disciplines',
			'client_employees',
			'clients',
			'days_to_respond_froms',
			'denial_reasons',
			'denial_types',
			'disciplines',
			'facilities',
			'facility_types',
			'guest_portals',
			'incoming_documents',
			'insurance_provider_appeal_levels',
			'insurance_provider_insurance_types',
			'insurance_providers',
			'insurance_types',
			'integrations',
			'invite_tokens',
			'not_defendable_reasons',
			'outgoing_documents',
			'patients',
			'permissions',
			'phinxlog',
			'reference_numbers',
			'roles',
			'roles_permissions',
			'services',
			'specialties',
			'user_logins',
			'users',
			'users_roles',
			'withdrawn_reasons',
			'utc_reasons',
			'vendors',
			'vendor_specialties',
		],
		'tenantTableName' => 'clients',
		'tenantForeignKeyName' => 'client_id',
		'userTableName' => 'users',
		'tablesAlreadyHaveTenantKey' => [
			'cases',
			'facilities',
		],
		'tablesDontNeedTenantKey' => [
			'agency_outgoing_profile', // Always linked to agency
			'appeal_levels', // global table
			'appeal_types', // global table
			'case_outcomes', // global table
			'case_types', 	// all clients share the same types
			'client_types', // all clients share the same types
			'days_to_respond_froms', // global
			'denial_types', // global table
			'disciplines', // global table
			'facility_types', // global table
			'invite_tokens', // tokens are not client specific
			'insurance_types', // global table
			'not_defendable_reasons', // global table
			'permissions', // global table
			'phinxlog',     // this is migration log table
			'reference_numbers', // global table
			'specialties',  // specialties and vendors are not client specific
			'user_logins',  // doesn't need client_id column since it's linked to user
			'vendors',      // specialties and vendors are not client specific
			'vendor_specialties', // specialties and vendors are not client specific
			'withdrawn_reasons' // global table
		],
	]
];
