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
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'locality_district')) {
                        $table->string('locality_district')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_location')) {
                        $table->string('locality_location')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_state')) {
                        $table->string('locality_state')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'locality_village')) {
                        $table->string('locality_village')->nullable();
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
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['locality_district', 'locality_location', 'locality_state', 'locality_village']);
                });
            }
        }
    }
};
