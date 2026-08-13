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
        // 1. orphan_care_funds
        if (Schema::hasTable('orphan_care_funds') && !Schema::hasColumn('orphan_care_funds', 'agency_project_no')) {
            Schema::table('orphan_care_funds', function (Blueprint $table) {
                $table->string('agency_project_no')->nullable()->after('orphan_care_project_id');
            });

            // Backfill agency_project_no from orphan_care_projects
            if (Schema::hasTable('orphan_care_projects') && Schema::hasColumn('orphan_care_projects', 'agency_project_no')) {
                $projects = DB::table('orphan_care_projects')->pluck('agency_project_no', 'id');
                foreach ($projects as $projectId => $agencyNo) {
                    if ($agencyNo) {
                        DB::table('orphan_care_funds')
                            ->where('orphan_care_project_id', $projectId)
                            ->update(['agency_project_no' => $agencyNo]);
                    }
                }
            }
        }

        // 2. differently_abled_funds
        if (Schema::hasTable('differently_abled_funds') && !Schema::hasColumn('differently_abled_funds', 'agency_project_no')) {
            Schema::table('differently_abled_funds', function (Blueprint $table) {
                $table->string('agency_project_no')->nullable()->after('differently_abled_project_id');
            });

            // Backfill agency_project_no from differently_abled_projects
            if (Schema::hasTable('differently_abled_projects') && Schema::hasColumn('differently_abled_projects', 'agency_project_no')) {
                $projects = DB::table('differently_abled_projects')->pluck('agency_project_no', 'id');
                foreach ($projects as $projectId => $agencyNo) {
                    if ($agencyNo) {
                        DB::table('differently_abled_funds')
                            ->where('differently_abled_project_id', $projectId)
                            ->update(['agency_project_no' => $agencyNo]);
                    }
                }
            }
        }

        // 3. family_aid_funds
        if (Schema::hasTable('family_aid_funds') && !Schema::hasColumn('family_aid_funds', 'agency_project_no')) {
            Schema::table('family_aid_funds', function (Blueprint $table) {
                $table->string('agency_project_no')->nullable()->after('family_aid_project_id');
            });

            // Backfill agency_project_no from family_aid_projects
            if (Schema::hasTable('family_aid_projects') && Schema::hasColumn('family_aid_projects', 'agency_project_no')) {
                $projects = DB::table('family_aid_projects')->pluck('agency_project_no', 'id');
                foreach ($projects as $projectId => $agencyNo) {
                    if ($agencyNo) {
                        DB::table('family_aid_funds')
                            ->where('family_aid_project_id', $projectId)
                            ->update(['agency_project_no' => $agencyNo]);
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
        if (Schema::hasTable('orphan_care_funds') && Schema::hasColumn('orphan_care_funds', 'agency_project_no')) {
            Schema::table('orphan_care_funds', function (Blueprint $table) {
                $table->dropColumn('agency_project_no');
            });
        }

        if (Schema::hasTable('differently_abled_funds') && Schema::hasColumn('differently_abled_funds', 'agency_project_no')) {
            Schema::table('differently_abled_funds', function (Blueprint $table) {
                $table->dropColumn('agency_project_no');
            });
        }

        if (Schema::hasTable('family_aid_funds') && Schema::hasColumn('family_aid_funds', 'agency_project_no')) {
            Schema::table('family_aid_funds', function (Blueprint $table) {
                $table->dropColumn('agency_project_no');
            });
        }
    }
};
