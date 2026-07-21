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
        Schema::create('orphan_care_programmes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orphan_care_project_id');
            $table->string('programme_name');
            $table->string('date')->nullable();
            $table->string('place')->nullable();
            
            // Checklist fields
            $table->boolean('present_ticked')->default(false);
            $table->boolean('photo_ticked')->default(false);
            $table->boolean('marklist_ticked')->default(false);
            $table->boolean('thanks_letter_ticked')->default(false);
            $table->boolean('report_form_ticked')->default(false);
            $table->boolean('other_document_ticked')->default(false);

            $table->timestamps();

            $table->foreign('orphan_care_project_id')
                  ->references('id')
                  ->on('orphan_care_projects')
                  ->onDelete('cascade');
        });

        // Migrate existing JSON data to the new table
        if (Schema::hasTable('orphan_care_projects') && Schema::hasColumn('orphan_care_projects', 'programmes_data')) {
            $projects = DB::table('orphan_care_projects')->whereNotNull('programmes_data')->get();
            foreach ($projects as $project) {
                $programmes = json_decode($project->programmes_data, true);
                if (is_array($programmes)) {
                    foreach ($programmes as $prog) {
                        DB::table('orphan_care_programmes')->insert([
                            'orphan_care_project_id' => $project->id,
                            'programme_name' => $prog['programme_name'] ?? 'N/A',
                            'date' => $prog['date'] ?? null,
                            'place' => $prog['place'] ?? null,
                            
                            'present_ticked' => !empty($prog['present_ticked']),
                            'present_ticked_at' => $prog['present_ticked_at'] ?? null,
                            
                            'photo_ticked' => !empty($prog['photo_ticked']),
                            'photo_ticked_at' => $prog['photo_ticked_at'] ?? null,
                            
                            'marklist_ticked' => !empty($prog['marklist_ticked']),
                            'marklist_ticked_at' => $prog['marklist_ticked_at'] ?? null,
                            
                            'thanks_letter_ticked' => !empty($prog['thanks_letter_ticked']),
                            'thanks_letter_ticked_at' => $prog['thanks_letter_ticked_at'] ?? null,
                            
                            'report_form_ticked' => !empty($prog['report_form_ticked']),
                            'report_form_ticked_at' => $prog['report_form_ticked_at'] ?? null,
                            
                            'other_document_ticked' => !empty($prog['other_document_ticked']),
                            'other_document_ticked_at' => $prog['other_document_ticked_at'] ?? null,
                            
                            'created_at' => $prog['created_at'] ?? now(),
                            'updated_at' => $prog['updated_at'] ?? now(),
                        ]);
                    }
                }
            }

            // Drop column from orphan_care_projects
            Schema::table('orphan_care_projects', function (Blueprint $table) {
                $table->dropColumn('programmes_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back column if not exists
        if (Schema::hasTable('orphan_care_projects') && !Schema::hasColumn('orphan_care_projects', 'programmes_data')) {
            Schema::table('orphan_care_projects', function (Blueprint $table) {
                $table->json('programmes_data')->nullable();
            });

            // Back up data to JSON column
            $programmes = DB::table('orphan_care_programmes')->get();
            $grouped = [];
            foreach ($programmes as $prog) {
                $grouped[$prog->orphan_care_project_id][] = [
                    'programme_name' => $prog->programme_name,
                    'date' => $prog->date,
                    'place' => $prog->place,
                    'present_ticked' => $prog->present_ticked,
                    'present_ticked_at' => $prog->present_ticked_at,
                    'photo_ticked' => $prog->photo_ticked,
                    'photo_ticked_at' => $prog->photo_ticked_at,
                    'marklist_ticked' => $prog->marklist_ticked,
                    'marklist_ticked_at' => $prog->marklist_ticked_at,
                    'thanks_letter_ticked' => $prog->thanks_letter_ticked,
                    'thanks_letter_ticked_at' => $prog->thanks_letter_ticked_at,
                    'report_form_ticked' => $prog->report_form_ticked,
                    'report_form_ticked_at' => $prog->report_form_ticked_at,
                    'other_document_ticked' => $prog->other_document_ticked,
                    'other_document_ticked_at' => $prog->other_document_ticked_at,
                    'created_at' => $prog->created_at,
                    'updated_at' => $prog->updated_at,
                ];
            }

            foreach ($grouped as $projectId => $data) {
                DB::table('orphan_care_projects')
                    ->where('id', $projectId)
                    ->update(['programmes_data' => json_encode($data)]);
            }
        }

        Schema::dropIfExists('orphan_care_programmes');
    }
};
