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
        // Cleanup social_aid_funds deprecated project_id column if present
        if (Schema::hasTable('social_aid_funds') && Schema::hasColumn('social_aid_funds', 'project_id')) {
            Schema::table('social_aid_funds', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('social_aid_funds') && !Schema::hasColumn('social_aid_funds', 'project_id')) {
            Schema::table('social_aid_funds', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable();
            });
        }
    }
};
