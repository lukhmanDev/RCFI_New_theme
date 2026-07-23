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
        if (Schema::hasTable('hospital_clinic_applications') && Schema::hasColumn('hospital_clinic_applications', 'project_area')) {
            Schema::table('hospital_clinic_applications', function (Blueprint $table) {
                $table->string('project_area')->nullable()->change();
            });
        }

        if (Schema::hasTable('shop_other_applications') && Schema::hasColumn('shop_other_applications', 'project_area')) {
            Schema::table('shop_other_applications', function (Blueprint $table) {
                $table->string('project_area')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
