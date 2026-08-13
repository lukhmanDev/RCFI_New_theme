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
            'orphan_care_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'general_applications',
            'house_applications',
            'education_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'cultural_center_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'current_beneficiaries')) {
                        $table->string('current_beneficiaries')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'whatsapp_number')) {
                        $table->string('whatsapp_number')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'recommender_name')) {
                        $table->string('recommender_name')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'recommender_org')) {
                        $table->string('recommender_org')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'recommender_phone')) {
                        $table->string('recommender_phone')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'recommender_position')) {
                        $table->string('recommender_position')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'agency_name')) {
                        $table->string('agency_name')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'application_date')) {
                        $table->string('application_date')->nullable();
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
            'orphan_care_applications',
            'family_aid_applications',
            'differently_abled_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'general_applications',
            'house_applications',
            'education_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'cultural_center_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $columns = ['current_beneficiaries', 'whatsapp_number', 'recommender_name', 'recommender_org', 'recommender_phone', 'recommender_position', 'agency_name', 'application_date'];
                    foreach ($columns as $col) {
                        if (Schema::hasColumn($tableName, $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
            }
        }
    }
};
