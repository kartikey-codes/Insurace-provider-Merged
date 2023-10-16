<?php

declare(strict_types=1);

namespace App\Lib\NpiUtility;

class NpiOrganizationResult
{
	public string $name;
	public string $organization_name;
	public int $number;
	public bool $organizational_subpart; // YES/NO
	public bool $active; // A

	public ?string $authorized_official_first_name;
	public ?string $authorized_official_last_name;
	public ?string $authorized_official_middle_name;
	public ?string $authorized_official_telephone_number;
	public ?string $authorized_official_title_or_position;
	public ?string $authorized_official_name_prefix;

	public int $created_epoch;
	public string $enumeration_date; // YYYY-MM-DD

	public string $last_updated; // YYYY-MM-DD
	public int $last_updated_epoch;

	public string $enumeration_type; // NPI-1 (Individual) / NPI-2 (Organization)

	public array $addresses = [];
	public array $identifiers = [];
	public array $other_names = [];
	public array $taxonomies = [];
}
