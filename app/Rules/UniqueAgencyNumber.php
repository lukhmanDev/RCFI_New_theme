<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UniqueAgencyNumber implements ValidationRule
{
    protected ?string $ignoreTable;
    protected mixed $ignoreId;
    protected mixed $ignoreApplicationId;

    /**
     * @param string|null $ignoreTable The table name of current record (if updating)
     * @param mixed $ignoreId The ID of the current record (if updating)
     * @param mixed $ignoreApplicationId The application ID if current record is linked to an application (e.g. project <-> application link)
     */
    public function __construct(?string $ignoreTable = null, mixed $ignoreId = null, mixed $ignoreApplicationId = null)
    {
        $this->ignoreTable = $ignoreTable;
        $this->ignoreId = $ignoreId;
        $this->ignoreApplicationId = $ignoreApplicationId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $agencyNumber = trim((string)$value);
        if ($agencyNumber === '') {
            return;
        }

        $appTables = [
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'house_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'general_applications',
            'applications',
        ];

        $projectTables = [
            'education_center_projects',
            'cultural_center_projects',
            'hospital_clinic_projects',
            'shop_other_projects',
            'house_projects',
            'drinking_water_group_projects',
            'drinking_water_individual_projects',
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects',
            'general_projects',
            'projects',
        ];

        // 1. Check Application Tables
        foreach ($appTables as $table) {
            if (!Schema::hasTable($table)) continue;

            $hasCol = Schema::hasColumn($table, 'agency_number');
            $hasMeta = Schema::hasColumn($table, 'meta');

            if ($hasCol || $hasMeta) {
                $query = DB::table($table)->where(function ($q) use ($hasCol, $hasMeta, $agencyNumber) {
                    if ($hasCol) {
                        $q->where('agency_number', $agencyNumber);
                    }
                    if ($hasMeta) {
                        $method = $hasCol ? 'orWhere' : 'where';
                        $q->$method(function ($sub) use ($agencyNumber) {
                            $sub->where('meta->agency_number', $agencyNumber)
                                ->orWhere('meta->agency_no', $agencyNumber);
                        });
                    }
                });

                if ($this->ignoreTable === $table && $this->ignoreId) {
                    $query->where('id', '!=', $this->ignoreId);
                }

                if ($this->ignoreApplicationId) {
                    $query->where('id', '!=', $this->ignoreApplicationId);
                }

                if ($query->exists()) {
                    $fail('The agency number "' . $agencyNumber . '" has already been taken.');
                    return;
                }
            }
        }

        // 2. Check Project Tables
        foreach ($projectTables as $table) {
            if (!Schema::hasTable($table)) continue;

            $hasProjNo = Schema::hasColumn($table, 'agency_project_no');
            $hasAgencyNum = Schema::hasColumn($table, 'agency_number');
            $hasAgencyId = Schema::hasColumn($table, 'agency_id');

            if ($hasProjNo || $hasAgencyNum || $hasAgencyId) {
                $query = DB::table($table)->where(function ($q) use ($hasProjNo, $hasAgencyNum, $hasAgencyId, $agencyNumber) {
                    $first = true;
                    if ($hasProjNo) {
                        $q->where('agency_project_no', $agencyNumber);
                        $first = false;
                    }
                    if ($hasAgencyNum) {
                        $first ? $q->where('agency_number', $agencyNumber) : $q->orWhere('agency_number', $agencyNumber);
                        $first = false;
                    }
                    if ($hasAgencyId) {
                        $first ? $q->where('agency_id', $agencyNumber) : $q->orWhere('agency_id', $agencyNumber);
                    }
                });

                if ($this->ignoreTable === $table && $this->ignoreId) {
                    $query->where('id', '!=', $this->ignoreId);
                }

                // If linked to the same application, allow matching agency number
                if ($this->ignoreApplicationId && Schema::hasColumn($table, 'application_id')) {
                    $query->where('application_id', '!=', $this->ignoreApplicationId);
                }

                if ($query->exists()) {
                    $fail('The agency number "' . $agencyNumber . '" has already been taken.');
                    return;
                }
            }
        }
    }
}
