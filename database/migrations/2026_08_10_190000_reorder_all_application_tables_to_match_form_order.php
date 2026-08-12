<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reorder columns across all application tables to match application form field model.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        $tablesConfig = [
            'education_center_applications' => [
                'applicant_name', 'committee_name', 'reg_number', 'year', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code', 'families_in_mahallu',
                'site_has_building', 'status_of_current_building', 'status_of_current_building_other', 'students_boys', 'students_girls', 'total_students', 'education_center_nearby', 'distance_education_center', 'syllabus',
                'project_type', 'requirement', 'building_area_sq', 'land_area_sq', 'num_classrooms', 'num_students', 'amount_requested', 'legal_approvals_status', 'permitted_type', 'area',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'cultural_center_applications' => [
                'applicant_name', 'committee_name', 'reg_number', 'year', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'post', 'village', 'panchayat', 'panchayath', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code', 'families_in_mahallu',
                'site_has_building', 'status_of_current_building', 'status_of_current_building_other', 'cultural_center_nearby', 'distance_cultural_centre', 'benefited_households',
                'project_type', 'requirement', 'building_area_sq', 'land_area_sq', 'amount_requested', 'legal_approvals_status', 'permitted_type', 'area',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'hospital_clinic_applications' => [
                'applicant_name', 'committee_name', 'reg_number', 'year', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code', 'families_in_mahallu',
                'site_has_building', 'status_of_current_building', 'status_of_current_building_other', 'education_center_nearby', 'distance_education_center',
                'project_type', 'requirement', 'building_area_sq', 'land_area_sq', 'amount_requested', 'legal_approvals_status', 'permitted_type', 'area',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'shops_other_applications' => [
                'applicant_name', 'committee_name', 'reg_number', 'year', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code', 'families_in_mahallu',
                'site_has_building', 'status_of_current_building', 'status_of_current_building_other', 'education_center_nearby', 'distance_education_center',
                'project_type', 'requirement', 'building_area_sq', 'land_area_sq', 'amount_requested', 'legal_approvals_status', 'permitted_type', 'area',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'house_applications' => [
                'applicant_name', 'father_husband_name', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code',
                'family_members', 'monthly_income', 'occupation',
                'requirement', 'building_area_sq', 'land_area_sq', 'amount_requested', 'legal_approvals_status', 'permitted_type', 'area',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'drinking_water_group_applications' => [
                'applicant_name', 'committee_name', 'reg_number', 'year', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code', 'families_in_mahallu',
                'benefited_households', 'water_source', 'project_type', 'requirement', 'land_area_sq', 'amount_requested', 'legal_approvals_status',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'drinking_water_individual_applications' => [
                'applicant_name', 'father_husband_name', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'submitted_before', 'received_support_before', 'financial_support_purpose',
                'mahallu_name', 'locality_place', 'locality_post', 'locality_village', 'locality_panchayath', 'locality_district', 'locality_state', 'locality_pin_code',
                'family_members', 'water_source', 'project_type', 'requirement', 'land_area_sq', 'amount_requested', 'legal_approvals_status',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'differently_abled_applications' => [
                'applicant_name', 'guardian_name', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'disability_type', 'disability_percentage', 'monthly_income', 'support_requested', 'amount_requested',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'family_aid_applications' => [
                'applicant_name', 'father_husband_name', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'family_members', 'monthly_income', 'occupation', 'support_requested', 'amount_requested',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'general_applications' => [
                'applicant_name', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'support_requested', 'amount_requested',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
            'orphan_care_applications' => [
                'applicant_name', 'orphan_name', 'guardian_name', 'contact_email', 'contact_number_1', 'contact_number_2',
                'house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code',
                'date_of_birth', 'gender', 'school_college_name', 'class_course', 'support_requested', 'amount_requested',
                'additional_note', 'details', 'recommendation_name', 'recommendation_organization', 'recommendation_organization_other', 'recommendation_phone', 'recommendation_position',
            ],
        ];

        $standardTail = ['status', 'rejected_reason', 'agency_number', 'agency_name', 'project_id', 'created_at', 'updated_at'];

        foreach ($tablesConfig as $tableName => $desiredOrder) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $rawCols = DB::select("SHOW FULL COLUMNS FROM `{$tableName}`");
            $colMap = [];
            foreach ($rawCols as $c) {
                $colMap[$c->Field] = $c;
            }

            $orderedList = [];
            foreach ($desiredOrder as $col) {
                if (isset($colMap[$col]) && !in_array($col, $orderedList)) {
                    $orderedList[] = $col;
                }
            }

            foreach ($standardTail as $col) {
                if (isset($colMap[$col]) && !in_array($col, $orderedList)) {
                    $orderedList[] = $col;
                }
            }

            foreach ($colMap as $field => $col) {
                if ($field !== 'id' && !in_array($field, $orderedList)) {
                    $orderedList[] = $field;
                }
            }

            $prev = 'id';
            foreach ($orderedList as $colName) {
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

                $sql = "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$colName}` {$type} {$collation} {$null} {$default} {$extra} AFTER `{$prev}`";

                try {
                    DB::statement($sql);
                } catch (\Exception $e) {
                    // Ignore column reorder error if constraint blocks
                }

                $prev = $colName;
            }
        }
    }

    public function down(): void
    {
    }
};
