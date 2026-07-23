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
            'cultural_center_projects',
            'education_center_projects',
            'hospital_clinic_projects',
            'shop_other_projects',
            'applications',
            'projects',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'area_sqft')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('area_sqft');
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
            'cultural_center_projects',
            'education_center_projects',
            'hospital_clinic_projects',
            'shop_other_projects',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'area_sqft')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->integer('area_sqft')->nullable();
                });
            }
        }
    }
};
