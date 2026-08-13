<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectDocument;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create project_documents table
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            
            // Generate columns for checklists and uploads (avoiding duplicates)
            $createdColumns = [];
            foreach (ProjectDocument::$docColumnMap as $docName => $column) {
                if (in_array($column, $createdColumns)) {
                    continue;
                }
                $table->string($column)->default('0');
                $table->timestamp($column . '_ticked_at')->nullable();
                $createdColumns[] = $column;
            }

            $table->timestamps();

            $table->index(['project_id', 'project_type']);
        });

        // 2. Migrate existing files data
        $existingFiles = DB::table('project_files')->where('name', '!=', 'photos')->get();
        
        $grouped = [];
        foreach ($existingFiles as $file) {
            $key = $file->project_type . '|' . $file->project_id;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'project_id' => $file->project_id,
                    'project_type' => $file->project_type,
                    'files' => []
                ];
            }
            $grouped[$key]['files'][] = $file;
        }

        foreach ($grouped as $projectKey => $data) {
            $insertData = [
                'project_id' => $data['project_id'],
                'project_type' => $data['project_type'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Initialize all columns to '0'
            foreach (ProjectDocument::$docColumnMap as $docName => $column) {
                $insertData[$column] = '0';
                $insertData[$column . '_ticked_at'] = null;
            }

            // Populate the migrated values
            foreach ($data['files'] as $file) {
                $column = ProjectDocument::$docColumnMap[$file->name] ?? null;
                if ($column) {
                    $insertData[$column] = $file->path;
                    $insertData[$column . '_ticked_at'] = $file->created_at;
                }
            }

            DB::table('project_documents')->insert($insertData);
        }

        // 3. Initialize checklist rows for all remaining existing projects
        $projectClasses = [
            'education_center_projects' => \App\Models\EducationCenterProject::class,
            'cultural_center_projects' => \App\Models\CulturalCenterProject::class,
            'hospital_clinic_projects' => \App\Models\HospitalClinicProject::class,
            'shop_other_projects' => \App\Models\ShopOtherProject::class,
            'house_projects' => \App\Models\HouseProject::class,
            'drinking_water_group_projects' => \App\Models\DrinkingWaterGroupProject::class,
            'drinking_water_individual_projects' => \App\Models\DrinkingWaterIndividualProject::class,
            'orphan_care_projects' => \App\Models\OrphanCareProject::class,
            'differently_abled_projects' => \App\Models\DifferentlyAbledProject::class,
            'family_aid_projects' => \App\Models\FamilyAidProject::class,
            'general_projects' => \App\Models\GeneralProject::class,
        ];

        foreach ($projectClasses as $tableName => $modelClass) {
            $projects = DB::table($tableName)->get();
            foreach ($projects as $project) {
                $exists = DB::table('project_documents')
                    ->where('project_id', $project->id)
                    ->where('project_type', $modelClass)
                    ->exists();

                if (!$exists) {
                    $insertData = [
                        'project_id' => $project->id,
                        'project_type' => $modelClass,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    foreach (ProjectDocument::$docColumnMap as $docName => $column) {
                        $insertData[$column] = '0';
                        $insertData[$column . '_ticked_at'] = null;
                    }

                    DB::table('project_documents')->insert($insertData);
                }
            }
        }

        // 4. Clean up old checklist/file entries from project_files (leaving only photos)
        DB::table('project_files')->where('name', '!=', 'photos')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
