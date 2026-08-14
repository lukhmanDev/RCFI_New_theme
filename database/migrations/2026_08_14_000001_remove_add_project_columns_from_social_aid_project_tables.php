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
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects',
        ];

        $columnsToDrop = [
            'project_name',
            'sponsor',
            'project_spec',
            'theme',
            'subtheme',
            'activity',
            'engineer_id',
            'project_manager_id',
            'donor_id',
            'available_budget',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnsToDrop) {
                    $existingColumns = [];
                    foreach ($columnsToDrop as $col) {
                        if (Schema::hasColumn($tableName, $col)) {
                            $existingColumns[] = $col;
                        }
                    }
                    if (!empty($existingColumns)) {
                        $table->dropColumn($existingColumns);
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
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'project_name')) {
                        $table->string('project_name')->nullable()->after('project_id');
                    }
                    if (!Schema::hasColumn($tableName, 'sponsor')) {
                        $table->string('sponsor')->nullable()->after('project_name');
                    }
                });
            }
        }
    }
};
