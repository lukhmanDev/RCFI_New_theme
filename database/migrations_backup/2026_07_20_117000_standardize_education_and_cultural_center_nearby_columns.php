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
        // 1. education_center_applications
        if (Schema::hasTable('education_center_applications')) {
            Schema::table('education_center_applications', function (Blueprint $table) {
                if (Schema::hasColumn('education_center_applications', 'cultural_center_nearby') && !Schema::hasColumn('education_center_applications', 'education_center_nearby')) {
                    $table->renameColumn('cultural_center_nearby', 'education_center_nearby');
                } elseif (!Schema::hasColumn('education_center_applications', 'education_center_nearby')) {
                    $table->string('education_center_nearby')->nullable();
                }

                if (Schema::hasColumn('education_center_applications', 'distance_cultural_centre') && !Schema::hasColumn('education_center_applications', 'distance_education_center')) {
                    $table->renameColumn('distance_cultural_centre', 'distance_education_center');
                } elseif (!Schema::hasColumn('education_center_applications', 'distance_education_center')) {
                    $table->string('distance_education_center')->nullable();
                }
            });
        }

        // 2. cultural_center_applications
        if (Schema::hasTable('cultural_center_applications')) {
            Schema::table('cultural_center_applications', function (Blueprint $table) {
                if (Schema::hasColumn('cultural_center_applications', 'education_center_nearby') && !Schema::hasColumn('cultural_center_applications', 'cultural_center_nearby')) {
                    $table->renameColumn('education_center_nearby', 'cultural_center_nearby');
                } elseif (!Schema::hasColumn('cultural_center_applications', 'cultural_center_nearby')) {
                    $table->string('cultural_center_nearby')->nullable();
                }

                if (!Schema::hasColumn('cultural_center_applications', 'distance_cultural_centre')) {
                    $table->string('distance_cultural_centre')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
