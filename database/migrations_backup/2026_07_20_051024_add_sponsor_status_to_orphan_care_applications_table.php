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
        Schema::table('orphan_care_applications', function (Blueprint $table) {
            $table->string('sponsor_status')->default('Not Sponsored')->after('agency_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orphan_care_applications', function (Blueprint $table) {
            $table->dropColumn('sponsor_status');
        });
    }
};
