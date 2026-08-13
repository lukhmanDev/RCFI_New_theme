<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename project_files table to project_photos
        if (Schema::hasTable('project_files') && !Schema::hasTable('project_photos')) {
            Schema::rename('project_files', 'project_photos');
        }

        // 2. Drop photo columns from all project tables
        $tables = [
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
            'general_projects'
        ];

        $columnsToDrop = [
            'photo_before', 'photos_before',
            'photo_starting', 'photos_starting',
            'photo_inbetween', 'photos_inbetween',
            'photo_after', 'photos_after',
            'photo_banner', 'photos_banner',
            'photo_stone', 'photos_stone',
            'photo_inauguration', 'photos_inauguration',
            'photos', 'files'
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn($tableName, $col)) {
                    Schema::table($tableName, function (Blueprint $table) use ($col) {
                        try {
                            $table->dropColumn($col);
                        } catch (\Throwable $e) {
                            // Ignore if already dropped or fail safe
                        }
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project_photos') && !Schema::hasTable('project_files')) {
            Schema::rename('project_photos', 'project_files');
        }
    }
};
