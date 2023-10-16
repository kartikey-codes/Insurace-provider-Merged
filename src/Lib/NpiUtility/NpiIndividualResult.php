<?php

declare(strict_types=1);

namespace App\Lib\NpiUtility;

class NpiIndividualResult
{
	public string $name;
	public int $number;
	public bool $active; // A

	public string $first_name;
	public string $last_name;
	public string $credential;
	public bool $sole_proprietor;
	public string $gender;

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
