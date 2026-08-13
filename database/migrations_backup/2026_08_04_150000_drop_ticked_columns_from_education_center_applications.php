<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops the 4 ticked/unused columns from education_center_applications:
     * rooms, cluster_id, locality_location, locality_panchayat
     */
    public function up(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            // Drop foreign key on cluster_id first if it exists
            try {
                $table->dropForeign(['cluster_id']);
            } catch (\Exception $e) {
                // No foreign key to drop, continue
            }

            $columns = ['rooms', 'cluster_id', 'locality_location', 'locality_panchayat'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('education_center_applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education_center_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('education_center_applications', 'rooms')) {
                $table->string('rooms')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'cluster_id')) {
                $table->unsignedBigInteger('cluster_id')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'locality_location')) {
                $table->string('locality_location')->nullable();
            }
            if (!Schema::hasColumn('education_center_applications', 'locality_panchayat')) {
                $table->string('locality_panchayat')->nullable();
            }
        });
    }
};
