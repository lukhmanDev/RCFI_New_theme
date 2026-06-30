<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables = [
        'education_center_projects',
        'cultural_center_projects',
        'hospital_clinic_projects',
        'shop_other_projects',
        'house_projects',
        'drinking_water_group_projects',
        'drinking_water_individual_projects',
        'orphan_care_projects',
        'differently_abled_projects',
        'family_aid_projects',
        'general_projects'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the legacy single projects table
        Schema::dropIfExists('projects');

        // Create separate project tables
        foreach ($this->tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('project_id')->unique()->nullable();
                $table->string('agency_project_no')->nullable();
                $table->foreignId('donor_id')->nullable()->constrained('donors')->onDelete('set null');
                $table->foreignId('project_manager_id')->nullable()->constrained('users')->onDelete('set null');
                $table->decimal('available_budget', 15, 2)->default(0);
                $table->string('type_of_project');
                $table->text('remarks')->nullable();
                $table->integer('stage')->default(1);
                $table->string('status')->default('Pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
