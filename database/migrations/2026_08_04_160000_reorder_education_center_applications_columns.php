<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reorder columns in education_center_applications to match the form field order.
     * Uses ALTER TABLE ... MODIFY COLUMN ... AFTER ... (MySQL specific).
     *
     * Form section order:
     * 1. Applicant & Committee Details
     * 2. Details of Proposed Locality
     * 3. Current Status & Students
     * 4. Proposed Project Details
     * 5. Recommendation Details
     * 6. System fields
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Get the table's current columns to know their types before reordering
        $columns = DB::select("SHOW FULL COLUMNS FROM `education_center_applications`");
        $colMap = [];
        foreach ($columns as $col) {
            $colMap[$col->Field] = $col;
        }

        /**
         * Build the desired column order after the auto `id` column.
         * Only include columns that actually exist in the table.
         */
        $desiredOrder = [
            // --- Section 1: Applicant & Committee Details ---
            'applicant_name',
            'committee_name',
            'reg_number',
            'year',
            'pin_code',
            'location',
            'village',
            'post',
            'panchayath',
            'district',
            'state',
            'contact_number_1',
            'contact_number_2',
            'submitted_before',
            'received_support_before',
            'financial_support_purpose',
            // --- Section 2: Details of Proposed Locality ---
            'mahallu_name',
            'locality_pin_code',
            'locality_village',
            'locality_post',
            'locality_panchayath',
            'locality_district',
            'locality_state',
            'families_in_mahallu',
            // --- Section 3: Current Status & Students ---
            'site_has_building',
            'status_of_current_building',
            'students_boys',
            'students_girls',
            'education_center_nearby',
            'syllabus',
            'distance_education_center',
            // --- Section 4: Proposed Project Details ---
            'project_type',
            'requirement',
            'building_area_sq',
            'land_area_sq',
            'num_classrooms',
            'num_students',
            'amount_requested',
            'legal_approvals_status',
            'permitted_type',
            'area',
            'details',
            // --- Recommendation Details ---
            'recommendation_name',
            'recommendation_organization',
            'recommendation_organization_other',
            'recommendation_phone',
            'recommendation_position',
            // --- Application meta ---
            'status',
            'rejected_reason',
            'whatsapp_number',
            'agency_name',
            'agency_number',
            'agency_pin_code',
            'locality_pin',
            'locality_place',
            'locality_post',
            'current_beneficiaries',
            'financial_support_purpose',
            // --- System columns ---
            'project_id',
            'created_at',
            'updated_at',
        ];

        // Build ordered list of columns that exist in the table (skip missing ones)
        $existingOrdered = [];
        foreach ($desiredOrder as $col) {
            if (isset($colMap[$col]) && !in_array($col, $existingOrdered)) {
                $existingOrdered[] = $col;
            }
        }

        // Add any remaining columns not in our desired list (to avoid losing them)
        foreach ($colMap as $field => $col) {
            if ($field !== 'id' && !in_array($field, $existingOrdered)) {
                $existingOrdered[] = $field;
            }
        }

        // Generate and run the ALTER TABLE statement
        $prev = 'id';
        foreach ($existingOrdered as $colName) {
            if (!isset($colMap[$colName])) continue;

            $col = $colMap[$colName];
            $type = $col->Type;
            $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
            $default = '';
            if ($col->Default !== null) {
                $default = "DEFAULT '" . addslashes($col->Default) . "'";
            } elseif ($col->Null === 'YES') {
                $default = 'DEFAULT NULL';
            }
            $extra = $col->Extra ? $col->Extra : '';
            $collation = $col->Collation ? "CHARACTER SET utf8mb4 COLLATE {$col->Collation}" : '';

            $sql = "ALTER TABLE `education_center_applications` MODIFY COLUMN `{$colName}` {$type} {$collation} {$null} {$default} {$extra} AFTER `{$prev}`";

            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // If a column fails (e.g., duplicate in desired list), skip it
            }

            $prev = $colName;
        }
    }

    /**
     * Down: no sensible reverse for reordering.
     */
    public function down(): void
    {
        // Column reordering is not easily reversible
    }
};
