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

        // 1. orphan_care_funds
        if (Schema::hasTable('orphan_care_funds') && Schema::hasColumn('orphan_care_funds', 'orphan_care_project_id')) {
            $fkList = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('CONSTRAINT_SCHEMA', $dbName)
                ->where('TABLE_NAME', 'orphan_care_funds')
                ->where('COLUMN_NAME', 'orphan_care_project_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->pluck('CONSTRAINT_NAME');

            foreach ($fkList as $fkName) {
                DB::statement("ALTER TABLE orphan_care_funds DROP FOREIGN KEY `{$fkName}`");
            }

            Schema::table('orphan_care_funds', function (Blueprint $table) {
                $table->dropColumn('orphan_care_project_id');
            });
        }

        // 2. differently_abled_funds
        if (Schema::hasTable('differently_abled_funds') && Schema::hasColumn('differently_abled_funds', 'differently_abled_project_id')) {
            $fkList = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('CONSTRAINT_SCHEMA', $dbName)
                ->where('TABLE_NAME', 'differently_abled_funds')
                ->where('COLUMN_NAME', 'differently_abled_project_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->pluck('CONSTRAINT_NAME');

            foreach ($fkList as $fkName) {
                DB::statement("ALTER TABLE differently_abled_funds DROP FOREIGN KEY `{$fkName}`");
            }

            Schema::table('differently_abled_funds', function (Blueprint $table) {
                $table->dropColumn('differently_abled_project_id');
            });
        }

        // 3. family_aid_funds
        if (Schema::hasTable('family_aid_funds') && Schema::hasColumn('family_aid_funds', 'family_aid_project_id')) {
            $fkList = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('CONSTRAINT_SCHEMA', $dbName)
                ->where('TABLE_NAME', 'family_aid_funds')
                ->where('COLUMN_NAME', 'family_aid_project_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->pluck('CONSTRAINT_NAME');

            foreach ($fkList as $fkName) {
                DB::statement("ALTER TABLE family_aid_funds DROP FOREIGN KEY `{$fkName}`");
            }

            Schema::table('family_aid_funds', function (Blueprint $table) {
                $table->dropColumn('family_aid_project_id');
            });
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
