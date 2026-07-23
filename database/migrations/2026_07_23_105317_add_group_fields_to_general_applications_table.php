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
        Schema::table('general_applications', function (Blueprint $table) {
            $table->string('application_type')->default('Individual')->nullable()->after('applicant_name');
            $table->string('organization_name')->nullable()->after('application_type');
            $table->string('unit')->nullable()->after('organization_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_applications', function (Blueprint $table) {
            $table->dropColumn(['application_type', 'organization_name', 'unit']);
        });
    }
};
