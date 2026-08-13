<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orphan_care_funds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orphan_care_project_id');
            $table->string('date');
            $table->decimal('amount', 15, 2);
            $table->string('agency');
            $table->timestamps();

            $table->foreign('orphan_care_project_id')
                  ->references('id')
                  ->on('orphan_care_projects')
                  ->onDelete('cascade');
        });

        // Migrate existing JSON data to the new table
        if (Schema::hasTable('orphan_care_projects') && Schema::hasColumn('orphan_care_projects', 'financial_data')) {
            $projects = DB::table('orphan_care_projects')->whereNotNull('financial_data')->get();
            foreach ($projects as $project) {
                $financials = json_decode($project->financial_data, true);
                if (is_array($financials)) {
                    foreach ($financials as $row) {
                        DB::table('orphan_care_funds')->insert([
                            'orphan_care_project_id' => $project->id,
                            'date' => $row['date'] ?? null,
                            'amount' => $row['amount'] ?? 0.00,
                            'agency' => $row['agency'] ?? 'N/A',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Drop column from orphan_care_projects
            Schema::table('orphan_care_projects', function (Blueprint $table) {
                $table->dropColumn('financial_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back column if not exists
        if (Schema::hasTable('orphan_care_projects') && !Schema::hasColumn('orphan_care_projects', 'financial_data')) {
            Schema::table('orphan_care_projects', function (Blueprint $table) {
                $table->json('financial_data')->nullable();
            });

            // Back up data to JSON column
            $funds = DB::table('orphan_care_funds')->get();
            $grouped = [];
            foreach ($funds as $fund) {
                $grouped[$fund->orphan_care_project_id][] = [
                    'date' => $fund->date,
                    'amount' => (float)$fund->amount,
                    'agency' => $fund->agency,
                ];
            }

            foreach ($grouped as $projectId => $data) {
                DB::table('orphan_care_projects')
                    ->where('id', $projectId)
                    ->update(['financial_data' => json_encode($data)]);
            }
        }

        Schema::dropIfExists('orphan_care_funds');
    }
};
