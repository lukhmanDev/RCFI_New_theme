<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'orphan_care_applications',
            'family_care_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'house_applications',
            'general_applications',
            'drinking_water_individual_applications',
            'drinking_water_group_applications',
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'applicant_addresses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableObj) use ($table) {
                    if (Schema::hasColumn($tableObj->getTable(), 'location')) {
                        $tableObj->dropColumn('location');
                    }
                    if (Schema::hasColumn($tableObj->getTable(), 'locality_location')) {
                        $tableObj->dropColumn('locality_location');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'orphan_care_applications',
            'family_care_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'house_applications',
            'general_applications',
            'drinking_water_individual_applications',
            'drinking_water_group_applications',
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'applicant_addresses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableObj) use ($table) {
                    if (!Schema::hasColumn($tableObj->getTable(), 'location')) {
                        $tableObj->string('location')->nullable();
                    }
                });
            }
        }
    }
};
