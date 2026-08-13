<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('education_center_applications', 'recommender_name')) {
                $table->string('recommender_name')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommender_org')) {
                $table->string('recommender_org')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommender_org_other')) {
                $table->string('recommender_org_other')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommender_phone')) {
                $table->string('recommender_phone')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommender_position')) {
                $table->string('recommender_position')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommendation_name')) {
                $table->string('recommendation_name')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommendation_organization')) {
                $table->string('recommendation_organization')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommendation_organization_other')) {
                $table->string('recommendation_organization_other')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommendation_phone')) {
                $table->string('recommendation_phone')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'recommendation_position')) {
                $table->string('recommendation_position')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            $cols = [
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
            foreach ($cols as $c) {
                if (Schema::hasColumn('education_center_applications', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
