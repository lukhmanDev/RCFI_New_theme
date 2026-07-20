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
            'applications'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'category')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('category');
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
            'applications'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'category')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('category')->nullable();
                });
            }
        }
    }
};
