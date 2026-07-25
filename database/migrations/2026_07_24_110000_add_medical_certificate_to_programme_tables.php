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
            'differently_abled_programmes',
            'orphan_care_programmes',
            'family_aid_programmes',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'medical_certificate_ticked')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->boolean('medical_certificate_ticked')->default(false)->after('report_form_ticked');
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
            'differently_abled_programmes',
            'orphan_care_programmes',
            'family_aid_programmes',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'medical_certificate_ticked')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('medical_certificate_ticked');
                });
            }
        }
    }
};
