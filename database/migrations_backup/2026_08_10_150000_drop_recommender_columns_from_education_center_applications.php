<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            $colsToDrop = [
                'recommender_name',
                'recommender_org',
                'recommender_org_other',
                'recommender_phone',
                'recommender_position',
                'recommendation_name',
                'recommendation_organization',
                'recommendation_organization_other',
                'recommendation_phone',
                'recommendation_position',
            ];

            foreach ($colsToDrop as $col) {
                if (Schema::hasColumn('education_center_applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            $table->string('recommender_name')->nullable();
            $table->string('recommender_org')->nullable();
            $table->string('recommender_org_other')->nullable();
            $table->string('recommender_phone')->nullable();
            $table->string('recommender_position')->nullable();
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();
        });
    }
};
