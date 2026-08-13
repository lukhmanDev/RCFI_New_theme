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
            'cultural_center_applications',
            'education_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'house_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'orphan_care_applications',
            'general_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'locality_pin_code')) {
                        $table->string('locality_pin_code')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_location')) {
                        $table->string('locality_location')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_place')) {
                        $table->string('locality_place')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_village')) {
                        $table->string('locality_village')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_post')) {
                        $table->string('locality_post')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_panchayath')) {
                        $table->string('locality_panchayath')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_panchayat')) {
                        $table->string('locality_panchayat')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_district')) {
                        $table->string('locality_district')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_state')) {
                        $table->string('locality_state')->nullable();
                    }

                    if (in_array($tableName, ['drinking_water_group_applications', 'drinking_water_individual_applications'])) {
                        if (!Schema::hasColumn($tableName, 'land_owner_pin')) {
                            $table->string('land_owner_pin')->nullable();
                        }
                        if (!Schema::hasColumn($tableName, 'land_owner_village')) {
                            $table->string('land_owner_village')->nullable();
                        }
                        if (!Schema::hasColumn($tableName, 'land_owner_state')) {
                            $table->string('land_owner_state')->nullable();
                        }
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
            'education_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'house_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'orphan_care_applications',
            'general_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $colsToDrop = [];
                    foreach (['locality_pin_code', 'locality_location', 'locality_place', 'locality_village', 'locality_post', 'locality_panchayath', 'locality_panchayat', 'locality_district', 'locality_state', 'land_owner_pin', 'land_owner_village', 'land_owner_state'] as $col) {
                        if (Schema::hasColumn($tableName, $col)) {
                            $colsToDrop[] = $col;
                        }
                    }
                    if (!empty($colsToDrop)) {
                        $table->dropColumn($colsToDrop);
                    }
                });
            }
        }
    }
};
