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
        if (Schema::hasTable('orphan_care_projects') && !Schema::hasColumn('orphan_care_projects', 'programmes_data')) {
            Schema::table('orphan_care_projects', function (Blueprint $table) {
                $table->json('programmes_data')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orphan_care_projects') && Schema::hasColumn('orphan_care_projects', 'programmes_data')) {
            Schema::table('orphan_care_projects', function (Blueprint $table) {
                $table->dropColumn('programmes_data');
            });
        }
    }
};
