<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop whatsapp_number, current_beneficiaries, application_date
     * from education_center_applications table.
     */
    public function up(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            $columns = ['whatsapp_number', 'current_beneficiaries', 'application_date'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('education_center_applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('education_center_applications', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'current_beneficiaries')) {
                $table->string('current_beneficiaries')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'application_date')) {
                $table->string('application_date')->nullable();
            }
        });
    }
};
