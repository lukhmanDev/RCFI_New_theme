<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. education_center_applications: rename education_center_nearby -> cultural_center_nearby
        if (Schema::hasTable('education_center_applications')) {
            if (Schema::hasColumn('education_center_applications', 'education_center_nearby') && !Schema::hasColumn('education_center_applications', 'cultural_center_nearby')) {
                Schema::table('education_center_applications', function (Blueprint $table) {
                    $table->renameColumn('education_center_nearby', 'cultural_center_nearby');
                });
            } elseif (!Schema::hasColumn('education_center_applications', 'cultural_center_nearby')) {
                Schema::table('education_center_applications', function (Blueprint $table) {
                    $table->string('cultural_center_nearby')->nullable();
                });
            }
        }

        // 2. cultural_center_applications: rename cultural_center_nearby -> education_center_nearby
        if (Schema::hasTable('cultural_center_applications')) {
            if (Schema::hasColumn('cultural_center_applications', 'cultural_center_nearby') && !Schema::hasColumn('cultural_center_applications', 'education_center_nearby')) {
                Schema::table('cultural_center_applications', function (Blueprint $table) {
                    $table->renameColumn('cultural_center_nearby', 'education_center_nearby');
                });
            } elseif (!Schema::hasColumn('cultural_center_applications', 'education_center_nearby')) {
                Schema::table('cultural_center_applications', function (Blueprint $table) {
                    $table->string('education_center_nearby')->nullable();
                });
            }
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
