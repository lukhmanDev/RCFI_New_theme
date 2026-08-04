<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop locality_location column from all application tables and ensure locality_place exists.
     */
    public function up(): void
    {
        $tables = [
            'cultural_center_applications',
            'differently_abled_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'education_center_applications',
            'family_aid_applications',
            'general_applications',
            'hospital_clinic_applications',
            'house_applications',
            'orphan_care_applications',
            'shop_other_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'locality_place')) {
                        $table->string('locality_place')->nullable();
                    }
                    if (Schema::hasColumn($tableName, 'locality_location')) {
                        $table->dropColumn('locality_location');
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
            'cultural_center_applications',
            'differently_abled_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'education_center_applications',
            'family_aid_applications',
            'general_applications',
            'hospital_clinic_applications',
            'house_applications',
            'orphan_care_applications',
            'shop_other_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'locality_location')) {
                        $table->string('locality_location')->nullable();
                    }
                });
            }
        }
    }
};
