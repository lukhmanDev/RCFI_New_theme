<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'orphan_care_projects', 
            'family_aid_projects', 
            'differently_abled_projects',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'applicant_addresses'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'location')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('location')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'orphan_care_projects', 
            'family_aid_projects', 
            'differently_abled_projects',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'applicant_addresses'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'location')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('location');
                });
            }
        }
    }
};
