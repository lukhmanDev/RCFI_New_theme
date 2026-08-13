<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (used in tests) cannot handle FK-constrained column drops; skip for non-MySQL
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tableName = 'shop_other_applications';
        if (!Schema::hasTable($tableName)) {
            return;
        }

        // Drop leftover duplicate recommender_* columns if present
        $dupCols = ['recommender_name', 'recommender_org', 'recommender_org_other', 'recommender_phone', 'recommender_position'];
        $colsToDrop = [];
        foreach ($dupCols as $c) {
            if (Schema::hasColumn($tableName, $c)) {
                $colsToDrop[] = $c;
            }
        }
        if (!empty($colsToDrop)) {
            Schema::table($tableName, function (Blueprint $table) use ($colsToDrop) {
                $table->dropColumn($colsToDrop);
            });
        }

        $orderedColumns = [
            'applicant_name' => 'varchar(255) NULL',
            'committee_name' => 'varchar(255) NULL',
            'reg_number' => 'varchar(255) NULL',
            'year' => 'varchar(255) NULL',
            'pin_code' => 'varchar(255) NULL',
            'place' => 'varchar(255) NULL',
            'village' => 'varchar(255) NULL',
            'post_office' => 'varchar(255) NULL',
            'panchayat' => 'varchar(255) NULL',
            'district' => 'varchar(255) NULL',
            'state' => 'varchar(255) NULL',
            'contact_number_1' => 'varchar(255) NULL',
            'contact_number_2' => 'varchar(255) NULL',
            'mahallu_name' => 'varchar(255) NULL',
            'locality_pin_code' => 'varchar(255) NULL',
            'locality_place' => 'varchar(255) NULL',
            'locality_village' => 'varchar(255) NULL',
            'locality_post' => 'varchar(255) NULL',
            'locality_panchayath' => 'varchar(255) NULL',
            'locality_district' => 'varchar(255) NULL',
            'locality_state' => 'varchar(255) NULL',
            'families_in_mahallu' => 'varchar(255) NULL',
            'site_has_building' => 'varchar(255) NULL',
            'status_of_current_building' => 'varchar(255) NULL',
            'status_of_current_building_other' => 'varchar(255) NULL',
            'num_rooms' => 'varchar(255) NULL',
            'office_shop' => 'varchar(255) NULL',
            'building_area_sq' => 'varchar(255) NULL',
            'amount_requested' => 'decimal(15,2) NULL',
            'legal_approvals_status' => 'varchar(255) NULL',
            'permitted_type' => 'varchar(255) NULL',
            'area' => 'varchar(255) NULL',
            'project_area' => 'varchar(255) NULL',
            'additional_note' => 'text NULL',
            'recommendation_name' => 'varchar(255) NULL',
            'recommendation_organization' => 'varchar(255) NULL',
            'recommendation_organization_other' => 'varchar(255) NULL',
            'recommendation_phone' => 'varchar(255) NULL',
            'recommendation_position' => 'varchar(255) NULL',
            'contact_email' => 'varchar(255) NULL',
            'status' => "varchar(255) NOT NULL DEFAULT 'Pending'",
            'rejected_reason' => 'text NULL',
            'agency_number' => 'varchar(255) NULL',
            'agency_name' => 'varchar(255) NULL',
            'created_at' => 'timestamp NULL',
            'updated_at' => 'timestamp NULL'
        ];

        $prevColumn = 'id';
        foreach ($orderedColumns as $column => $definition) {
            $exists = DB::select("SHOW COLUMNS FROM `shop_other_applications` LIKE '{$column}'");
            if (!empty($exists)) {
                DB::statement("ALTER TABLE `shop_other_applications` MODIFY COLUMN `{$column}` {$definition} AFTER `{$prevColumn}`");
                $prevColumn = $column;
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
