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
        Schema::table('orphan_care_projects', function (Blueprint $table) {
            $columnsToDrop = ['donor_id', 'project_manager_id', 'engineer_id', 'available_budget'];

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('orphan_care_projects', $col)) {
                    if (in_array($col, ['donor_id', 'project_manager_id', 'engineer_id'])) {
                        try {
                            $table->dropForeign([$col]);
                        } catch (\Exception $e) {
                            // Ignore if foreign key constraint does not exist
                        }
                    }
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
        Schema::table('orphan_care_projects', function (Blueprint $table) {
            $table->foreignId('donor_id')->nullable()->constrained('donors')->onDelete('set null');
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('engineer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('available_budget', 15, 2)->default(0);
        });
    }
};
