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

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('project_name')->nullable()->after('project_id');
                $table->string('sponsor')->nullable()->after('project_name');
                $table->text('project_spec')->nullable()->after('sponsor');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['project_name', 'sponsor', 'project_spec']);
            });
        }
    }
};
