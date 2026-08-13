<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables = [
        'projects',
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
        'general_projects',
        'social_aid_projects',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'total_beneficiary_peoples')) {
                        $table->integer('total_beneficiary_peoples')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'total_family')) {
                        $table->integer('total_family')->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'total_beneficiary_peoples')) {
                        $table->dropColumn('total_beneficiary_peoples');
                    }
                    if (Schema::hasColumn($tableName, 'total_family')) {
                        $table->dropColumn('total_family');
                    }
                });
            }
        }
    }
};
