<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

        $addressColumns = [
            'house_name',
            'place',
            'post_office',
            'village',
            'panchayat',
            'district',
            'state',
            'pin_code',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) use ($table, $addressColumns) {
                foreach ($addressColumns as $column) {
                    if (!Schema::hasColumn($table, $column)) {
                        $t->text($column)->nullable();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // No-op
    }
};
