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
        $isSqlite = DB::getDriverName() === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            Schema::disableForeignKeyConstraints();
        }

        // 1. orphan_care_funds
        if (Schema::hasTable('orphan_care_funds') && Schema::hasColumn('orphan_care_funds', 'orphan_care_project_id')) {
            if (!$isSqlite) {
                try { DB::statement('ALTER TABLE orphan_care_funds DROP FOREIGN KEY orphan_care_funds_orphan_care_project_id_foreign'); } catch (\Throwable $e) {}
            }
            Schema::table('orphan_care_funds', function (Blueprint $table) use ($isSqlite) {
                if ($isSqlite) {
                    try { $table->dropForeign(['orphan_care_project_id']); } catch (\Throwable $e) {}
                }
                $table->dropColumn('orphan_care_project_id');
            });
        }

        // 2. differently_abled_funds
        if (Schema::hasTable('differently_abled_funds') && Schema::hasColumn('differently_abled_funds', 'differently_abled_project_id')) {
            if (!$isSqlite) {
                try { DB::statement('ALTER TABLE differently_abled_funds DROP FOREIGN KEY fk_da_funds_proj_id'); } catch (\Throwable $e) {}
                try { DB::statement('ALTER TABLE differently_abled_funds DROP FOREIGN KEY differently_abled_funds_differently_abled_project_id_foreign'); } catch (\Throwable $e) {}
            }
            Schema::table('differently_abled_funds', function (Blueprint $table) use ($isSqlite) {
                if ($isSqlite) {
                    try { $table->dropForeign(['differently_abled_project_id']); } catch (\Throwable $e) {}
                }
                $table->dropColumn('differently_abled_project_id');
            });
        }

        // 3. family_aid_funds
        if (Schema::hasTable('family_aid_funds') && Schema::hasColumn('family_aid_funds', 'family_aid_project_id')) {
            if (!$isSqlite) {
                try { DB::statement('ALTER TABLE family_aid_funds DROP FOREIGN KEY fk_fa_funds_proj_id'); } catch (\Throwable $e) {}
                try { DB::statement('ALTER TABLE family_aid_funds DROP FOREIGN KEY family_aid_funds_family_aid_project_id_foreign'); } catch (\Throwable $e) {}
            }
            Schema::table('family_aid_funds', function (Blueprint $table) use ($isSqlite) {
                if ($isSqlite) {
                    try { $table->dropForeign(['family_aid_project_id']); } catch (\Throwable $e) {}
                }
                $table->dropColumn('family_aid_project_id');
            });
        }

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            Schema::enableForeignKeyConstraints();
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
