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
            'education_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'house_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'orphan_care_applications',
            'general_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'submitted_before')) {
                        $table->string('submitted_before')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'received_support_before')) {
                        $table->string('received_support_before')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'financial_support_purpose')) {
                        $table->string('financial_support_purpose')->nullable();
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
            'education_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'house_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'orphan_care_applications',
            'general_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $cols = [];
                    if (Schema::hasColumn($tableName, 'financial_support_purpose')) {
                        $cols[] = 'financial_support_purpose';
                    }
                    if (!empty($cols)) {
                        $table->dropColumn($cols);
                    }
                });
            }
        }
    }
};
