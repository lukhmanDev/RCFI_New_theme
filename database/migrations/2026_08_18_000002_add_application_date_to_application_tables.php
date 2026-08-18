<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'orphan_care_applications',
        'differently_abled_applications',
        'family_aid_applications',
        'general_applications',
        'drinking_water_individual_applications',
        'drinking_water_group_applications',
        'house_applications',
        'education_center_applications',
        'cultural_center_applications',
        'hospital_clinic_applications',
        'shop_other_applications'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'application_date')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->date('application_date')->nullable()->after('agency_name');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'application_date')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('application_date');
                });
            }
        }
    }
};
