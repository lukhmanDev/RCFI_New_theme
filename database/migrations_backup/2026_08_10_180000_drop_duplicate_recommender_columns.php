<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shops_other_applications',
            'house_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'general_applications',
            'orphan_care_applications',
        ];

        $recommenderCols = [
            'recommender_name',
            'recommender_org',
            'recommender_org_other',
            'recommender_phone',
            'recommender_position',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $recommenderCols) {
                    foreach ($recommenderCols as $col) {
                        if (Schema::hasColumn($tableName, $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // No roll back required for dropped duplicate columns
    }
};
