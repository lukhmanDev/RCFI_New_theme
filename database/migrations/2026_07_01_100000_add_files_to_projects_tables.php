<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables = [
        'education_center_projects',
        'cultural_center_projects',
        'hospital_clinic_projects',
        'shop_other_projects',
        'house_projects',
        'drinking_water_group_projects',
        'drinking_water_individual_projects',
        'orphan_care_projects',
        'differently_abled_projects',
        'family_aid_projects',
        'general_projects'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $tableCol) {
                $tableCol->text('files')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $tableCol) {
                $tableCol->dropColumn('files');
            });
        }
    }
};
