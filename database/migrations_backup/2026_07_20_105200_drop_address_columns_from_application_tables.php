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
        ];

        $columnsToDrop = [
            'house_name',
            'place',
            'post_office',
            'post',
            'village',
            'panchayat',
            'panchayath',
            'district',
            'state',
            'pin_code',
            'pin',
            'pincode',
            'location',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnsToDrop) {
                    foreach ($columnsToDrop as $column) {
                        if (Schema::hasColumn($tableName, $column)) {
                            $table->dropColumn($column);
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
        // Re-adding address columns if rolled back
        $tables = [
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
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('house_name')->nullable();
                    $table->string('place')->nullable();
                    $table->string('post_office')->nullable();
                    $table->string('village')->nullable();
                    $table->string('panchayat')->nullable();
                    $table->string('district')->nullable();
                    $table->string('state')->nullable();
                    $table->string('pin_code')->nullable();
                    $table->string('location')->nullable();
                });
            }
        }
    }
};
