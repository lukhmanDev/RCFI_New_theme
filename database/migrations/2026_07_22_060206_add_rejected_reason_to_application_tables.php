<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'cultural_center_applications',
        'differently_abled_applications',
        'drinking_water_group_applications',
        'drinking_water_individual_applications',
        'education_center_applications',
        'family_aid_applications',
        'general_applications',
        'hospital_clinic_applications',
        'house_applications',
        'orphan_care_applications',
        'shop_other_applications',
        'applications'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'rejected_reason')) {
                        $table->text('rejected_reason')->nullable()->after('status');
                    }
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
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'rejected_reason')) {
                        $table->dropColumn('rejected_reason');
                    }
                });
            }
        }
    }
};
