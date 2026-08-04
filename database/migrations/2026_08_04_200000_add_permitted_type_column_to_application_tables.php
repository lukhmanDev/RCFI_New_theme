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
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'permitted_type')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'legal_approvals_status')) {
                        $table->string('permitted_type')->nullable()->after('legal_approvals_status');
                    } else {
                        $table->string('permitted_type')->nullable();
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
        $tables = [
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
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'permitted_type')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('permitted_type');
                });
            }
        }
    }
};
