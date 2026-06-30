<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $tables = [
        'Education Center' => 'education_center_applications',
        'Cultural Center' => 'cultural_center_applications',
        'Hospital or Clinics' => 'hospital_clinic_applications',
        'Shops and Others' => 'shop_other_applications',
        'House' => 'house_applications',
        'Drinking Water - Group Level' => 'drinking_water_group_applications',
        'Drinking Water - Individual Level' => 'drinking_water_individual_applications',
        'Orphan Care' => 'orphan_care_applications',
        'Differently Abled' => 'differently_abled_applications',
        'Family Aid' => 'family_aid_applications',
        'General' => 'general_applications',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create all 11 new category tables
        foreach ($this->tables as $categoryName => $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('applicant_name');
                $table->string('category');
                $table->integer('amount_requested')->nullable();
                $table->string('status')->default('Pending');
                $table->string('contact_email')->nullable();
                $table->text('details')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        // 2. Migrate existing data from old table to new tables
        if (Schema::hasTable('applications')) {
            $existing = DB::table('applications')->get();
            foreach ($existing as $app) {
                $targetTable = $this->tables[$app->category] ?? null;
                if ($targetTable) {
                    DB::table($targetTable)->insert([
                        'id' => $app->id,
                        'applicant_name' => $app->applicant_name,
                        'category' => $app->category,
                        'amount_requested' => $app->amount_requested,
                        'status' => $app->status,
                        'contact_email' => $app->contact_email,
                        'details' => $app->details,
                        'meta' => $app->meta,
                        'created_at' => $app->created_at,
                        'updated_at' => $app->updated_at,
                    ]);
                }
            }

            // 3. Drop the old applications table
            Schema::dropIfExists('applications');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recreate the old applications table
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // 2. Copy data back from all category tables to the old table
        foreach ($this->tables as $categoryName => $tableName) {
            if (Schema::hasTable($tableName)) {
                $records = DB::table($tableName)->get();
                foreach ($records as $app) {
                    DB::table('applications')->insert([
                        'id' => $app->id,
                        'applicant_name' => $app->applicant_name,
                        'category' => $app->category,
                        'amount_requested' => $app->amount_requested,
                        'status' => $app->status,
                        'contact_email' => $app->contact_email,
                        'details' => $app->details,
                        'meta' => $app->meta,
                        'created_at' => $app->created_at,
                        'updated_at' => $app->updated_at,
                    ]);
                }

                // 3. Drop the category table
                Schema::dropIfExists($tableName);
            }
        }
    }
};
