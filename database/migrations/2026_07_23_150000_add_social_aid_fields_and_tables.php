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
        // Add columns to differently_abled_applications
        Schema::table('differently_abled_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('differently_abled_applications', 'cluster_id')) {
                $table->unsignedBigInteger('cluster_id')->nullable()->after('status');
                $table->foreign('cluster_id', 'fk_da_app_cluster_id')->references('id')->on('clusters')->onDelete('set null');
            }
            if (!Schema::hasColumn('differently_abled_applications', 'agency_number')) {
                $table->string('agency_number')->nullable()->after('cluster_id');
            }
            if (!Schema::hasColumn('differently_abled_applications', 'sponsor_status')) {
                $table->string('sponsor_status')->default('Not Sponsored')->after('agency_number');
            }
            if (!Schema::hasColumn('differently_abled_applications', 'student_photo')) {
                $table->string('student_photo')->nullable()->after('sponsor_status');
            }
        });

        // Add columns to family_aid_applications
        Schema::table('family_aid_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('family_aid_applications', 'cluster_id')) {
                $table->unsignedBigInteger('cluster_id')->nullable()->after('status');
                $table->foreign('cluster_id', 'fk_fa_app_cluster_id')->references('id')->on('clusters')->onDelete('set null');
            }
            if (!Schema::hasColumn('family_aid_applications', 'agency_number')) {
                $table->string('agency_number')->nullable()->after('cluster_id');
            }
            if (!Schema::hasColumn('family_aid_applications', 'sponsor_status')) {
                $table->string('sponsor_status')->default('Not Sponsored')->after('agency_number');
            }
            if (!Schema::hasColumn('family_aid_applications', 'student_photo')) {
                $table->string('student_photo')->nullable()->after('sponsor_status');
            }
        });

        // Create differently_abled_funds
        if (!Schema::hasTable('differently_abled_funds')) {
            Schema::create('differently_abled_funds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('differently_abled_project_id');
                $table->string('date');
                $table->decimal('amount', 15, 2);
                $table->string('agency');
                $table->timestamps();

                $table->foreign('differently_abled_project_id', 'fk_da_funds_proj_id')
                      ->references('id')
                      ->on('differently_abled_projects')
                      ->onDelete('cascade');
            });
        }

        // Create differently_abled_programmes
        if (!Schema::hasTable('differently_abled_programmes')) {
            Schema::create('differently_abled_programmes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('differently_abled_project_id');
                $table->string('programme_name');
                $table->string('date')->nullable();
                $table->string('place')->nullable();
                
                $table->boolean('present_ticked')->default(false);
                $table->boolean('photo_ticked')->default(false);
                $table->boolean('marklist_ticked')->default(false);
                $table->boolean('thanks_letter_ticked')->default(false);
                $table->boolean('report_form_ticked')->default(false);
                $table->boolean('other_document_ticked')->default(false);

                $table->timestamps();

                $table->foreign('differently_abled_project_id', 'fk_da_prog_proj_id')
                      ->references('id')
                      ->on('differently_abled_projects')
                      ->onDelete('cascade');
            });
        }

        // Create family_aid_funds
        if (!Schema::hasTable('family_aid_funds')) {
            Schema::create('family_aid_funds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('family_aid_project_id');
                $table->string('date');
                $table->decimal('amount', 15, 2);
                $table->string('agency');
                $table->timestamps();

                $table->foreign('family_aid_project_id', 'fk_fa_funds_proj_id')
                      ->references('id')
                      ->on('family_aid_projects')
                      ->onDelete('cascade');
            });
        }

        // Create family_aid_programmes
        if (!Schema::hasTable('family_aid_programmes')) {
            Schema::create('family_aid_programmes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('family_aid_project_id');
                $table->string('programme_name');
                $table->string('date')->nullable();
                $table->string('place')->nullable();
                
                $table->boolean('present_ticked')->default(false);
                $table->boolean('photo_ticked')->default(false);
                $table->boolean('marklist_ticked')->default(false);
                $table->boolean('thanks_letter_ticked')->default(false);
                $table->boolean('report_form_ticked')->default(false);
                $table->boolean('other_document_ticked')->default(false);

                $table->timestamps();

                $table->foreign('family_aid_project_id', 'fk_fa_prog_proj_id')
                      ->references('id')
                      ->on('family_aid_projects')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_aid_programmes');
        Schema::dropIfExists('family_aid_funds');
        Schema::dropIfExists('differently_abled_programmes');
        Schema::dropIfExists('differently_abled_funds');

        Schema::table('family_aid_applications', function (Blueprint $table) {
            if (Schema::hasColumn('family_aid_applications', 'cluster_id')) {
                $table->dropForeign('fk_fa_app_cluster_id');
                $table->dropColumn(['cluster_id', 'agency_number', 'sponsor_status', 'student_photo']);
            }
        });

        Schema::table('differently_abled_applications', function (Blueprint $table) {
            if (Schema::hasColumn('differently_abled_applications', 'cluster_id')) {
                $table->dropForeign('fk_da_app_cluster_id');
                $table->dropColumn(['cluster_id', 'agency_number', 'sponsor_status', 'student_photo']);
            }
        });
    }
};
