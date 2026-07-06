<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables = [
        'education_center_applications',
        'cultural_center_applications',
        'hospital_clinic_applications',
        'shop_other_applications',
        'house_applications',
        'drinking_water_group_applications',
        'drinking_water_individual_applications',
        'orphan_care_applications',
        'differently_abled_applications',
        'family_aid_applications',
        'general_applications',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $tableBlue) use ($table) {
                if (!Schema::hasColumn($table, 'house_name')) {
                    $tableBlue->string('house_name')->nullable();
                }
                if (!Schema::hasColumn($table, 'place')) {
                    $tableBlue->string('place')->nullable();
                }
                if (!Schema::hasColumn($table, 'post_office')) {
                    $tableBlue->string('post_office')->nullable();
                }
                if (!Schema::hasColumn($table, 'village')) {
                    // If village exists as a non-string type (like integer), we don't recreate it,
                    // but we can check if it exists at all
                    $tableBlue->string('village')->nullable();
                }
                if (!Schema::hasColumn($table, 'panchayat')) {
                    $tableBlue->string('panchayat')->nullable();
                }
                if (!Schema::hasColumn($table, 'district')) {
                    $tableBlue->string('district')->nullable();
                }
                if (!Schema::hasColumn($table, 'state')) {
                    $tableBlue->string('state')->nullable();
                }
                if (!Schema::hasColumn($table, 'pin_code')) {
                    $tableBlue->string('pin_code')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safety: we do not drop columns in down to prevent accidental data loss during rollback,
        // since some tables already had some of these columns.
    }
};
