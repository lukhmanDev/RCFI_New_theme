<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (used in tests) cannot drop columns that are part of a FK
        // definition in the original DDL; skip entirely for SQLite.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tableName = 'shop_other_applications';
        $columns = [
            'project_type',
            'submitted_before',
            'received_support_before',
            'financial_support_purpose',
            'cluster_id',
            'application_date',
            'whatsapp_number',
            'current_beneficiaries',
            'rooms',
            'locality_panchayat'
        ];

        if (!Schema::hasTable($tableName)) {
            return;
        }

        if (Schema::hasColumn($tableName, 'cluster_id')) {
            try {
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = ?
                      AND COLUMN_NAME = 'cluster_id'
                      AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$tableName]);

                foreach ($foreignKeys as $fk) {
                    $fkName = $fk->CONSTRAINT_NAME;
                    DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$fkName}`");
                }
            } catch (\Throwable $e) {}
        }

        $colsToDrop = [];
        foreach ($columns as $col) {
            if (Schema::hasColumn($tableName, $col)) {
                $colsToDrop[] = $col;
            }
        }

        if (!empty($colsToDrop)) {
            Schema::table($tableName, function (Blueprint $table) use ($colsToDrop) {
                $table->dropColumn($colsToDrop);
            });
        }
    }

    public function down(): void
    {
        // No-op
    }
};
