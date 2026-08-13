<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'hospital_clinic_applications';
        $columns = ['submitted_before', 'received_support_before', 'financial_support_purpose'];

        if (!Schema::hasTable($tableName)) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();
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

        if ($driver === 'sqlite') {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('hospital_clinic_applications')) {
            return;
        }

        Schema::table('hospital_clinic_applications', function (Blueprint $table) {
            $table->string('submitted_before')->nullable();
            $table->string('received_support_before')->nullable();
            $table->string('financial_support_purpose')->nullable();
        });
    }
};
