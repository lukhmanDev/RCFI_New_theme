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
        // 1. Create project_files table
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->string('name');
            $table->text('path');
            $table->timestamps();

            $table->index(['project_id', 'project_type']);
        });

        // 2. Create project_contractors table
        Schema::create('project_contractors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->string('contractor_name');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->string('type_of_contract')->nullable();
            $table->string('purpose_of_contract')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'project_type']);
        });

        // 3. Create project_expenses table
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->string('expense_name');
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('type'); // 'material' or 'spent'
            $table->timestamps();

            $table->index(['project_id', 'project_type']);
        });

        // 4. Add community_contributions and completion_details to project tables
        foreach ($this->tables as $table => $modelClass) {
            Schema::table($table, function (Blueprint $tableCol) {
                if (!Schema::hasColumn($tableCol->getTable(), 'community_contributions')) {
                    $tableCol->text('community_contributions')->nullable()->after('files');
                }
                if (!Schema::hasColumn($tableCol->getTable(), 'completion_details')) {
                    $tableCol->text('completion_details')->nullable()->after('files');
                }
            });
        }

        // 5. Migrate existing data
        foreach ($this->tables as $table => $modelClass) {
            $projects = DB::table($table)->get();

            foreach ($projects as $project) {
                // Parse files column
                if (!empty($project->files)) {
                    $files = json_decode($project->files, true);
                    if (is_array($files)) {
                        // Extract regular files
                        foreach ($files as $name => $path) {
                            if (in_array($name, ['contractors', 'photos', 'community_contributions', 'completion_details'])) {
                                continue;
                            }
                            if (!empty($path)) {
                                DB::table('project_files')->insert([
                                    'project_id' => $project->id,
                                    'project_type' => $modelClass,
                                    'name' => $name,
                                    'path' => $path,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        // Extract photos
                        if (!empty($files['photos']) && is_array($files['photos'])) {
                            DB::table('project_files')->insert([
                                'project_id' => $project->id,
                                'project_type' => $modelClass,
                                'name' => 'photos',
                                'path' => json_encode($files['photos']),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        // Extract contractors
                        if (!empty($files['contractors']) && is_array($files['contractors'])) {
                            foreach ($files['contractors'] as $c) {
                                DB::table('project_contractors')->insert([
                                    'project_id' => $project->id,
                                    'project_type' => $modelClass,
                                    'contractor_name' => $c['contractor_name'] ?? '',
                                    'phone' => $c['phone'] ?? null,
                                    'company_name' => $c['company_name'] ?? null,
                                    'address' => $c['address'] ?? null,
                                    'type_of_contract' => $c['type_of_contract'] ?? null,
                                    'purpose_of_contract' => $c['purpose_of_contract'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        // Extract community_contributions and completion_details
                        $updateData = [];
                        if (!empty($files['community_contributions'])) {
                            $updateData['community_contributions'] = json_encode($files['community_contributions']);
                        }
                        if (!empty($files['completion_details'])) {
                            $updateData['completion_details'] = json_encode($files['completion_details']);
                        }

                        if (!empty($updateData)) {
                            DB::table($table)->where('id', $project->id)->update($updateData);
                        }
                    }
                }

                // Parse materials column
                if (!empty($project->materials)) {
                    $materials = json_decode($project->materials, true);
                    if (is_array($materials)) {
                        foreach ($materials as $m) {
                            DB::table('project_expenses')->insert([
                                'project_id' => $project->id,
                                'project_type' => $modelClass,
                                'expense_name' => $m['material'] ?? '',
                                'quantity' => 1,
                                'amount' => $m['amount'] ?? 0,
                                'type' => 'material',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // Parse expenses column (if exists on the row)
                if (isset($project->expenses) && !empty($project->expenses)) {
                    $expenses = json_decode($project->expenses, true);
                    if (is_array($expenses)) {
                        foreach ($expenses as $e) {
                            DB::table('project_expenses')->insert([
                                'project_id' => $project->id,
                                'project_type' => $modelClass,
                                'expense_name' => $e['expense_name'] ?? '',
                                'quantity' => $e['quantity'] ?? 1,
                                'amount' => $e['amount'] ?? 0,
                                'type' => 'spent',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('project_contractors');
        Schema::dropIfExists('project_expenses');

        foreach ($this->tables as $table => $modelClass) {
            Schema::table($table, function (Blueprint $tableCol) {
                $tableCol->dropColumn(['community_contributions', 'completion_details']);
            });
        }
    }
};
