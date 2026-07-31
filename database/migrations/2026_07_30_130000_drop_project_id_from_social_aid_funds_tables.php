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
        $dbName = DB::getDatabaseName();
        $isSqlite = DB::getDriverName() === 'sqlite';

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // 1. orphan_care_funds
        if (Schema::hasTable('orphan_care_funds') && Schema::hasColumn('orphan_care_funds', 'orphan_care_project_id')) {
            Schema::table('orphan_care_funds', function (Blueprint $table) {
                try {
                    $table->dropForeign(['orphan_care_project_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('orphan_care_project_id');
            });
        }

        // 2. differently_abled_funds
        if (Schema::hasTable('differently_abled_funds') && Schema::hasColumn('differently_abled_funds', 'differently_abled_project_id')) {
            Schema::table('differently_abled_funds', function (Blueprint $table) {
                try {
                    $table->dropForeign(['differently_abled_project_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('differently_abled_project_id');
            });
        }

        // 3. family_aid_funds
        if (Schema::hasTable('family_aid_funds') && Schema::hasColumn('family_aid_funds', 'family_aid_project_id')) {
            Schema::table('family_aid_funds', function (Blueprint $table) {
                try {
                    $table->dropForeign(['family_aid_project_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('family_aid_project_id');
            });
        }

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orphan_care_funds') && !Schema::hasColumn('orphan_care_funds', 'orphan_care_project_id')) {
            Schema::table('orphan_care_funds', function (Blueprint $table) {
                $table->unsignedBigInteger('orphan_care_project_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('differently_abled_funds') && !Schema::hasColumn('differently_abled_funds', 'differently_abled_project_id')) {
            Schema::table('differently_abled_funds', function (Blueprint $table) {
                $table->unsignedBigInteger('differently_abled_project_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('family_aid_funds') && !Schema::hasColumn('family_aid_funds', 'family_aid_project_id')) {
            Schema::table('family_aid_funds', function (Blueprint $table) {
                $table->unsignedBigInteger('family_aid_project_id')->nullable()->after('id');
            });
        }
    }
};
