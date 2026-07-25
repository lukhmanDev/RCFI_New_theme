<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add indexes for 5000+ records scalability.
     */
    public function up(): void
    {
        $projectTables = [
            'general_projects',
            'education_center_projects',
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects',
            'house_projects',
            'hospital_clinic_projects',
            'cultural_center_projects',
            'shop_other_projects',
            'drinking_water_group_projects',
            'drinking_water_individual_projects',
        ];

        $applicationTables = [
            'general_applications',
            'education_center_applications',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'house_applications',
            'hospital_clinic_applications',
            'cultural_center_applications',
            'shop_other_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
        ];

        $columnsToIndex = [
            'application_id',
            'project_manager_id',
            'engineer_id',
            'donor_id',
            'status',
            'stage',
            'created_at'
        ];

        foreach ($projectTables as $table) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table, $columnsToIndex) {
                foreach ($columnsToIndex as $col) {
                    if (Schema::hasColumn($table, $col)) {
                        $indexName = "{$table}_{$col}_idx";
                        try {
                            $t->index($col, $indexName);
                        } catch (\Throwable $e) {
                            // Skip if index already exists
                        }
                    }
                }
            });
        }

        foreach ($applicationTables as $table) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach (['status', 'created_at'] as $col) {
                    if (Schema::hasColumn($table, $col)) {
                        $indexName = "{$table}_{$col}_idx";
                        try {
                            $t->index($col, $indexName);
                        } catch (\Throwable $e) {
                            // Skip if index already exists
                        }
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $projectTables = [
            'general_projects',
            'education_center_projects',
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects',
            'house_projects',
            'hospital_clinic_projects',
            'cultural_center_projects',
            'shop_other_projects',
            'drinking_water_group_projects',
            'drinking_water_individual_projects',
        ];

        $applicationTables = [
            'general_applications',
            'education_center_applications',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'house_applications',
            'hospital_clinic_applications',
            'cultural_center_applications',
            'shop_other_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
        ];

        $columnsToIndex = [
            'application_id',
            'project_manager_id',
            'engineer_id',
            'donor_id',
            'status',
            'stage',
            'created_at'
        ];

        foreach ($projectTables as $table) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table, $columnsToIndex) {
                foreach ($columnsToIndex as $col) {
                    if (Schema::hasColumn($table, $col)) {
                        $indexName = "{$table}_{$col}_idx";
                        try {
                            $t->dropIndex($indexName);
                        } catch (\Throwable $e) {}
                    }
                }
            });
        }

        foreach ($applicationTables as $table) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach (['status', 'created_at'] as $col) {
                    if (Schema::hasColumn($table, $col)) {
                        $indexName = "{$table}_{$col}_idx";
                        try {
                            $t->dropIndex($indexName);
                        } catch (\Throwable $e) {}
                    }
                }
            });
        }
    }
};
