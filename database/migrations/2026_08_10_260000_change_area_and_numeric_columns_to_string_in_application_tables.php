<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tables = [
            'house_applications',
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'general_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'orphan_care_applications',
            'family_aid_applications',
            'differently_abled_applications',
        ];

        $columnsToModify = [
            'building_area_sq',
            'land_area_sq',
            'area',
            'project_area',
            'own_place_details',
            'monthly_income',
            'other_income',
            'age',
            'num_children',
            'num_male_children',
            'num_female_children',
            'children_total',
            'children_male',
            'children_female',
            'families_in_mahallu',
            'num_classrooms',
            'num_students',
            'num_rooms',
            'year',
            'well_depth',
            'well_diameter',
            'siblings_male',
            'siblings_female',
            'siblings_total',
            'monthly_expense',
            'amount_requested',
            'num_male_benefited',
            'num_female_benefited',
            'num_benefited_people',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($columnsToModify as $column) {
                if (Schema::hasColumn($table, $column)) {
                    try {
                        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` VARCHAR(255) NULL");
                    } catch (\Exception $e) {
                        // Ignore if column modification fails
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
