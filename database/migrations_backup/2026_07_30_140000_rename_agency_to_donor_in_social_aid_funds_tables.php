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

        if (!$isSqlite) {
            // 1. Add UNIQUE constraint on donors.name if not already present
            $uniqueExists = DB::select("SHOW INDEX FROM donors WHERE Key_name = 'donors_name_unique' OR (Non_unique = 0 AND Column_name = 'name')");
            if (empty($uniqueExists)) {
                DB::statement("UPDATE donors SET name = TRIM(name)");
                DB::statement("ALTER TABLE donors ADD CONSTRAINT donors_name_unique UNIQUE (name)");
            }
        }

        $tables = ['orphan_care_funds', 'differently_abled_funds', 'family_aid_funds'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // Parse existing agency strings and backfill any missing donors into `donors` table
            if (Schema::hasColumn($tableName, 'agency')) {
                $rawAgencies = DB::table($tableName)->whereNotNull('agency')->pluck('agency');
                foreach ($rawAgencies as $raw) {
                    $cleanName = trim(explode(' (', $raw)[0]);
                    if ($cleanName !== '' && !DB::table('donors')->where('name', $cleanName)->exists()) {
                        $shortName = null;
                        if (preg_match('/\((.*?)\)/', $raw, $m)) {
                            $shortName = trim($m[1]);
                        }
                        DB::table('donors')->insert([
                            'name' => $cleanName,
                            'short_name' => $shortName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Rename agency column to donor
                Schema::table($tableName, function (Blueprint $table) {
                    $table->renameColumn('agency', 'donor');
                });
            }

            if (!$isSqlite) {
                // Update donor column values to extract just the Name (strip " (ShortName)")
                DB::statement("UPDATE {$tableName} SET donor = TRIM(SUBSTRING_INDEX(donor, ' (', 1)) WHERE donor LIKE '% (%'");

                // Ensure column definition & add foreign key constraint referencing donors.name
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->string('donor', 255)->change();
                    $table->foreign('donor', "{$tableName}_donor_foreign")
                          ->references('name')
                          ->on('donors')
                          ->onUpdate('cascade')
                          ->onDelete('restrict');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['orphan_care_funds', 'differently_abled_funds', 'family_aid_funds'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'donor')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        $table->dropForeign("{$tableName}_donor_foreign");
                    } catch (\Throwable $e) {}

                    $table->renameColumn('donor', 'agency');
                });
            }
        }

        try {
            Schema::table('donors', function (Blueprint $table) {
                $table->dropUnique('donors_name_unique');
            });
        } catch (\Throwable $e) {}
    }
};
