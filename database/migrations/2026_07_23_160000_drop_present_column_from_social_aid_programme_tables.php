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
        if (Schema::hasTable('orphan_care_programmes') && Schema::hasColumn('orphan_care_programmes', 'present')) {
            Schema::table('orphan_care_programmes', function (Blueprint $table) {
                $table->dropColumn('present');
            });
        }

        if (Schema::hasTable('differently_abled_programmes') && Schema::hasColumn('differently_abled_programmes', 'present')) {
            Schema::table('differently_abled_programmes', function (Blueprint $table) {
                $table->dropColumn('present');
            });
        }

        if (Schema::hasTable('family_aid_programmes') && Schema::hasColumn('family_aid_programmes', 'present')) {
            Schema::table('family_aid_programmes', function (Blueprint $table) {
                $table->dropColumn('present');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orphan_care_programmes') && !Schema::hasColumn('orphan_care_programmes', 'present')) {
            Schema::table('orphan_care_programmes', function (Blueprint $table) {
                $table->string('present')->nullable()->after('place');
            });
        }

        if (Schema::hasTable('differently_abled_programmes') && !Schema::hasColumn('differently_abled_programmes', 'present')) {
            Schema::table('differently_abled_programmes', function (Blueprint $table) {
                $table->string('present')->nullable()->after('place');
            });
        }

        if (Schema::hasTable('family_aid_programmes') && !Schema::hasColumn('family_aid_programmes', 'present')) {
            Schema::table('family_aid_programmes', function (Blueprint $table) {
                $table->string('present')->nullable()->after('place');
            });
        }
    }
};
