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
        Schema::table('education_center_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('education_center_applications', 'status_of_current_building_other')) {
                $table->string('status_of_current_building_other')->nullable()->after('status_of_current_building');
            }
            if (!Schema::hasColumn('education_center_applications', 'total_students')) {
                $table->integer('total_students')->nullable()->after('students_girls');
            }
        });

        Schema::table('cultural_center_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('cultural_center_applications', 'status_of_current_building_other')) {
                $table->string('status_of_current_building_other')->nullable()->after('status_of_current_building');
            }
        });

        Schema::table('hospital_clinic_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('hospital_clinic_applications', 'status_of_current_building')) {
                $table->string('status_of_current_building')->nullable()->after('site_has_building');
            }
            if (!Schema::hasColumn('hospital_clinic_applications', 'status_of_current_building_other')) {
                $table->string('status_of_current_building_other')->nullable()->after('status_of_current_building');
            }
        });

        Schema::table('shop_other_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_other_applications', 'status_of_current_building_other')) {
                $table->string('status_of_current_building_other')->nullable()->after('status_of_current_building');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            if (Schema::hasColumn('education_center_applications', 'status_of_current_building_other')) {
                $table->dropColumn('status_of_current_building_other');
            }
            if (Schema::hasColumn('education_center_applications', 'total_students')) {
                $table->dropColumn('total_students');
            }
        });

        Schema::table('cultural_center_applications', function (Blueprint $table) {
            if (Schema::hasColumn('cultural_center_applications', 'status_of_current_building_other')) {
                $table->dropColumn('status_of_current_building_other');
            }
        });

        Schema::table('hospital_clinic_applications', function (Blueprint $table) {
            if (Schema::hasColumn('hospital_clinic_applications', 'status_of_current_building_other')) {
                $table->dropColumn('status_of_current_building_other');
            }
            if (Schema::hasColumn('hospital_clinic_applications', 'status_of_current_building')) {
                $table->dropColumn('status_of_current_building');
            }
        });

        Schema::table('shop_other_applications', function (Blueprint $table) {
            if (Schema::hasColumn('shop_other_applications', 'status_of_current_building_other')) {
                $table->dropColumn('status_of_current_building_other');
            }
        });
    }
};
