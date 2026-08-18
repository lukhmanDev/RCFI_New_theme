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
        if (Schema::hasTable('orphan_care_applications') && !Schema::hasColumn('orphan_care_applications', 'current_beneficiaries')) {
            Schema::table('orphan_care_applications', function (Blueprint $table) {
                $table->string('current_beneficiaries')->nullable()->after('siblings_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orphan_care_applications') && Schema::hasColumn('orphan_care_applications', 'current_beneficiaries')) {
            Schema::table('orphan_care_applications', function (Blueprint $table) {
                $table->dropColumn('current_beneficiaries');
            });
        }
    }
};
