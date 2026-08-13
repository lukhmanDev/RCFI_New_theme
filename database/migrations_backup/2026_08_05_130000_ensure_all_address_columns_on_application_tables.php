<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $categoryRequiredColumns = [
            'education_center_applications' => ['committee_name', 'reg_number', 'year', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2', 'mahallu_name', 'locality_pin_code', 'locality_place', 'locality_village', 'locality_post', 'locality_panchayath', 'locality_district', 'locality_state', 'families_in_mahallu'],
            'cultural_center_applications' => ['committee_name', 'reg_number', 'year', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2', 'mahallu_name', 'locality_pin_code', 'locality_place', 'locality_village', 'locality_post', 'locality_panchayath', 'locality_district', 'locality_state', 'families_in_mahallu'],
            'hospital_clinic_applications' => ['committee_name', 'reg_number', 'year', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2', 'mahallu_name', 'locality_pin_code', 'locality_place', 'locality_village', 'locality_post', 'locality_panchayath', 'locality_district', 'locality_state', 'families_in_mahallu'],
            'shop_other_applications' => ['committee_name', 'reg_number', 'year', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2', 'mahallu_name', 'locality_pin_code', 'locality_place', 'locality_village', 'locality_post', 'locality_panchayath', 'locality_district', 'locality_state', 'families_in_mahallu'],
            'house_applications' => ['age', 'gender', 'father_name', 'mother_name', 'house_name', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
            'drinking_water_group_applications' => ['gender', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
            'drinking_water_individual_applications' => ['gender', 'job', 'monthly_income', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
            'orphan_care_applications' => ['dob', 'age', 'gender', 'aadhar_number', 'house_name', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
            'differently_abled_applications' => ['dob', 'age', 'gender', 'aadhar_number', 'father_name', 'mother_name', 'fathers_father', 'house_name', 'pin_code', 'place', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
            'family_aid_applications' => ['dob', 'age', 'gender', 'aadhar_number', 'father_name', 'mother_name', 'fathers_father', 'house_name', 'pin_code', 'place', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
            'general_applications' => ['age', 'sex', 'gender', 'father_name', 'mother_name', 'house_name', 'pin_code', 'place', 'village', 'post_office', 'panchayat', 'district', 'state', 'contact_number_1', 'contact_number_2'],
        ];

        foreach ($categoryRequiredColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table, $columns) {
                    foreach ($columns as $col) {
                        if (!Schema::hasColumn($table, $col)) {
                            if (in_array($col, ['age', 'year', 'monthly_income', 'families_in_mahallu'])) {
                                $t->integer($col)->nullable();
                            } else {
                                $t->text($col)->nullable();
                            }
                        }
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // No-op for safety
    }
};
