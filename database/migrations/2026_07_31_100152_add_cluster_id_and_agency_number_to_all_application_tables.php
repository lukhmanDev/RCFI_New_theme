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
                    if (!Schema::hasColumn($tableName, 'cluster_id')) {
                        $table->unsignedBigInteger('cluster_id')->nullable();
                        $table->foreign('cluster_id')->references('id')->on('clusters')->onDelete('set null');
                    }
                    if (!Schema::hasColumn($tableName, 'agency_number')) {
                        $table->string('agency_number')->nullable();
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
                    if (Schema::hasColumn($tableName, 'agency_number')) {
                        $table->dropColumn('agency_number');
                    }
                    if (Schema::hasColumn($tableName, 'cluster_id')) {
                        $table->dropForeign(['cluster_id']);
                        $table->dropColumn('cluster_id');
                    }
                });
            }
        }
    }
};
