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
        $tables = [
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects'
        ];

        $columnsToDrop = [
            // User / Management / Budget / Phase
            'donor_id',
            'project_manager_id',
            'engineer_id',
            'contractor_id',
            'available_budget',
            'project_phase',
            'specifications',
            'project_spec',
            'unit',

            // Photos
            'photo_before',
            'photos_before',
            'photo_starting',
            'photos_starting',
            'photo_inbetween',
            'photos_inbetween',
            'photo_after',
            'photos_after',
            'photo_banner',
            'photos_banner',
            'photo_stone',
            'photos_stone',
            'photo_inauguration',
            'photos_inauguration',

            // Construction Details & Expenses
            'completion_details',
            'community_contributions',
            'materials',
            'expenses',

            // Theme & Subtheme
            'theme',
            'subtheme',
            'activity'
        ];

        $driver = DB::getDriverName();

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn($tableName, $col)) {
                    $indexName = "{$tableName}_{$col}_idx";
                    try {
                        if ($driver === 'sqlite') {
                            DB::statement("DROP INDEX IF EXISTS \"{$indexName}\"");
                        } else {
                            DB::statement("DROP INDEX `{$indexName}` ON `{$tableName}`");
                        }
                    } catch (\Throwable $e) {
                        // Ignore
                    }

                    Schema::table($tableName, function (Blueprint $table) use ($col) {
                        if (in_array($col, ['donor_id', 'project_manager_id', 'engineer_id', 'contractor_id'])) {
                            try {
                                $table->dropForeign([$col]);
                            } catch (\Throwable $e) {
                                // Ignore
                            }
                        }

                        try {
                            $table->dropColumn($col);
                        } catch (\Throwable $e) {
                            // Ignore
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
        $tables = [
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects'
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'donor_id')) {
                    $table->foreignId('donor_id')->nullable()->constrained('donors')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'project_manager_id')) {
                    $table->foreignId('project_manager_id')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'engineer_id')) {
                    $table->foreignId('engineer_id')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'contractor_id')) {
                    $table->foreignId('contractor_id')->nullable()->constrained('contractors')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'available_budget')) {
                    $table->decimal('available_budget', 15, 2)->default(0);
                }
                if (!Schema::hasColumn($tableName, 'project_phase')) {
                    $table->string('project_phase')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'specifications')) {
                    $table->text('specifications')->nullable();
                }
            });
        }
    }
};
