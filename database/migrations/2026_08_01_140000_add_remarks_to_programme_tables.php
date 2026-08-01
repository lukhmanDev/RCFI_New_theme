<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['orphan_care_programmes', 'family_aid_programmes', 'differently_abled_programmes'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'remarks')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->text('remarks')->nullable()->after('place');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['orphan_care_programmes', 'family_aid_programmes', 'differently_abled_programmes'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'remarks')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('remarks');
                });
            }
        }
    }
};
