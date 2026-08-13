<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $tables = [
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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create project_statuses table
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->string('status')->nullable();
            $table->string('status_custom')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'project_type']);
        });

        // 2. Migrate existing status values
        foreach ($this->tables as $tableName => $modelClass) {
            if (Schema::hasColumn($tableName, 'project_phase')) {
                $projects = DB::table($tableName)->get();
                foreach ($projects as $project) {
                    DB::table('project_statuses')->insert([
                        'project_id' => $project->id,
                        'project_type' => $modelClass,
                        'status' => $project->project_phase ?? null,
                        'status_custom' => $project->project_phase_custom ?? null,
                        'created_at' => $project->created_at ?? now(),
                        'updated_at' => $project->updated_at ?? now(),
                    ]);
                }
            }
        }

        // 3. Drop columns project_phase and project_phase_custom from project tables
        foreach (array_keys($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'project_phase')) {
                    $table->dropColumn('project_phase');
                }
                if (Schema::hasColumn($table->getTable(), 'project_phase_custom')) {
                    $table->dropColumn('project_phase_custom');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To rollback:
        // 1. Re-add columns project_phase and project_phase_custom to project tables
        foreach (array_keys($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'project_phase')) {
                    $table->string('project_phase')->nullable()->after('status');
                }
                if (!Schema::hasColumn($table->getTable(), 'project_phase_custom')) {
                    $table->string('project_phase_custom')->nullable()->after('project_phase');
                }
            });
        }

        // 2. Restore data from project_statuses to project tables
        $statuses = DB::table('project_statuses')->get();
        foreach ($statuses as $row) {
            $tableName = null;
            foreach ($this->tables as $tName => $mClass) {
                if ($mClass === $row->project_type) {
                    $tableName = $tName;
                    break;
                }
            }

            if ($tableName) {
                DB::table($tableName)->where('id', $row->project_id)->update([
                    'project_phase' => $row->status,
                    'project_phase_custom' => $row->status_custom,
                ]);
            }
        }

        // 3. Drop project_statuses table
        Schema::dropIfExists('project_statuses');
    }
};
