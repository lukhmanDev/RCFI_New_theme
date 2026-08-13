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
        // 1. Create all 11 new category tables with clean explicit columns
        
        // cultural_center_applications
        Schema::create('cultural_center_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: General Details of Applicant / Organization
            $table->string('committee_name')->nullable();
            $table->string('reg_number')->nullable();
            $table->integer('year')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Details of Proposed Locality
            $table->string('mahallu_name')->nullable();
            $table->string('locality_pin_code')->nullable();
            $table->string('locality_place')->nullable();
            $table->string('locality_village')->nullable();
            $table->string('locality_post')->nullable();
            $table->string('locality_panchayath')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('families_in_mahallu')->nullable();

            // Section 3: Current Status & Beneficiaries
            $table->string('site_has_building')->nullable();
            $table->string('status_of_current_building')->nullable();
            $table->string('requirement')->nullable();
            $table->string('cultural_center_nearby')->nullable();
            $table->string('distance_cultural_centre')->nullable();
            $table->string('benefited_households')->nullable();

            // Section 4: Proposed Project Details
            $table->string('project_type')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->integer('land_area_sq')->nullable();
            $table->integer('rooms')->nullable();
            $table->string('num_beneficiaries')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('area')->nullable();

            // Recommendation Details
            $table->string('submitted_before')->nullable();
            $table->string('received_support_before')->nullable();
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // differently_abled_applications
        Schema::create('differently_abled_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Basic Information of Applicant
            $table->string('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('fathers_father')->nullable();
            $table->string('house_name')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Disability Details
            $table->string('disability_type')->nullable();
            $table->integer('disability_percentage')->nullable();
            $table->string('disability_level')->nullable();
            $table->string('disability_date')->nullable();
            $table->string('description')->nullable();

            // Section 3: Family & Livelihood Details
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('marital_status')->nullable();
            $table->integer('male_members')->nullable();
            $table->integer('female_members')->nullable();
            $table->integer('total_members')->nullable();
            $table->integer('people_with_disabilities')->nullable();
            $table->string('studying_institution')->nullable();
            $table->string('not_studying_reason')->nullable();
            $table->integer('monthly_cost')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('income_source')->nullable();
            $table->string('accommodation')->nullable();
            $table->string('health_status')->nullable();
            $table->string('other_help')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // drinking_water_group_applications
        Schema::create('drinking_water_group_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Personal Details of Applicant
            $table->string('gender')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Beneficiary Details Summary
            $table->integer('male_adults')->nullable();
            $table->integer('male_children')->nullable();
            $table->integer('female_adults')->nullable();
            $table->integer('female_children')->nullable();
            $table->integer('num_benefited_people')->nullable();
            $table->json('beneficiaries')->nullable();

            // Section 3: Owner of the Proposed Land
            $table->string('land_owner_name')->nullable();
            $table->string('land_owner_address')->nullable();
            $table->string('land_owner_place')->nullable();
            $table->string('land_owner_post')->nullable();
            $table->string('land_owner_panchayath')->nullable();
            $table->string('land_owner_district')->nullable();
            $table->string('land_owner_mobile')->nullable();

            // Section 4: Project & Well Details
            $table->string('well_type')->nullable();
            $table->integer('well_depth')->nullable();
            $table->string('legal_permissions')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // drinking_water_individual_applications
        Schema::create('drinking_water_individual_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Personal Details of Applicant
            $table->string('gender')->nullable();
            $table->string('job')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Beneficiary Details Summary
            $table->integer('num_male_benefited')->nullable();
            $table->integer('num_female_benefited')->nullable();
            $table->integer('num_benefited_people')->nullable();
            $table->json('beneficiaries')->nullable();

            // Section 3: Owner of Proposed Land
            $table->string('land_owner_name')->nullable();
            $table->string('land_owner_address')->nullable();
            $table->string('land_owner_place')->nullable();
            $table->string('land_owner_post')->nullable();
            $table->string('land_owner_panchayath')->nullable();
            $table->string('land_owner_district')->nullable();
            $table->string('land_owner_mobile')->nullable();

            // Section 4: Project & Well Details
            $table->string('well_type')->nullable();
            $table->integer('well_depth')->nullable();
            $table->string('well_diameter')->nullable();
            $table->string('land_nature')->nullable();
            $table->string('current_water_source')->nullable();
            $table->string('legal_permissions')->nullable();
            $table->string('need_pump')->nullable();
            $table->string('well_for_agriculture')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // education_center_applications
        Schema::create('education_center_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: General Details of Applicant / Organization
            $table->string('committee_name')->nullable();
            $table->string('reg_number')->nullable();
            $table->integer('year')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Details of Proposed Locality
            $table->string('mahallu_name')->nullable();
            $table->string('locality_pin_code')->nullable();
            $table->string('locality_place')->nullable();
            $table->string('locality_village')->nullable();
            $table->string('locality_post')->nullable();
            $table->string('locality_panchayath')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('families_in_mahallu')->nullable();

            // Section 3: Current Status & Students
            $table->string('site_has_building')->nullable();
            $table->string('status_of_current_building')->nullable();
            $table->string('requirement')->nullable();
            $table->string('education_center_nearby')->nullable();
            $table->string('distance_cultural_centre')->nullable();
            $table->string('syllabus')->nullable();
            $table->integer('students_boys')->nullable();
            $table->integer('students_girls')->nullable();

            // Section 4: Proposed Project Details
            $table->string('project_type')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->integer('land_area_sq')->nullable();
            $table->integer('num_classrooms')->nullable();
            $table->integer('num_students')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('area')->nullable();

            // Recommendation & Previous Support Details
            $table->string('submitted_before')->nullable();
            $table->string('received_support_before')->nullable();
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // family_aid_applications
        Schema::create('family_aid_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Personal Details of Applicant
            $table->string('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('fathers_father')->nullable();
            $table->string('house_name')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Family & Living Conditions
            $table->string('residence_info')->nullable();
            $table->string('own_house_condition')->nullable();
            $table->string('own_place_size')->nullable();
            $table->string('own_place_status')->nullable();

            // Section 3: Occupation & Income
            $table->string('occupation')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('other_income_sources')->nullable();
            $table->string('nri_status')->nullable();

            // Section 4: Health & Special Circumstances
            $table->string('health_status')->nullable();
            $table->text('routine_treatment_explanation')->nullable();
            $table->string('disability_status')->nullable();
            $table->text('chronic_patients_description')->nullable();

            // Section 5: Family Count & Livelihood
            $table->integer('children_male')->nullable();
            $table->integer('children_female')->nullable();
            $table->integer('children_total')->nullable();
            $table->string('sequel_status')->nullable();
            $table->text('welfare_assistance_areas')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // general_applications
        Schema::create('general_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Personal Details of Applicant
            $table->integer('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('gender')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('house_name')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Family Details
            $table->string('married')->nullable();
            $table->integer('male_family_members')->nullable();
            $table->integer('female_family_members')->nullable();
            $table->integer('total_family_members')->nullable();
            $table->integer('num_earning_members')->nullable();
            $table->string('education')->nullable();

            // Section 3: Financial & Health Details
            $table->string('occupation')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->integer('monthly_income_detail')->nullable();
            $table->integer('other_income')->nullable();
            $table->string('health_status')->nullable();

            // Section 4: Application & Accommodation Details
            $table->string('applying_for')->nullable();
            $table->string('expected_amount')->nullable();
            $table->text('accommodation_details')->nullable();
            $table->string('status_of_applicant')->nullable();
            $table->string('general_app_status')->nullable();
            $table->string('office_app_type')->nullable();
            $table->string('office_application_type')->nullable();
            $table->string('recommended_by')->nullable();
            $table->string('recommended_phone')->nullable();
            $table->string('block')->nullable();
            $table->string('ward')->nullable();
            $table->string('panchayat_municipality_corporation')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // hospital_clinic_applications
        Schema::create('hospital_clinic_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: General Details of Organization / Applicant
            $table->string('committee_name')->nullable();
            $table->string('reg_number')->nullable();
            $table->integer('year')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Details of Proposed Locality
            $table->string('mahallu_name')->nullable();
            $table->string('locality_pin_code')->nullable();
            $table->string('locality_place')->nullable();
            $table->string('locality_village')->nullable();
            $table->string('locality_post')->nullable();
            $table->string('locality_panchayath')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('families_in_mahallu')->nullable();

            // Section 3: Proposed Project Details
            $table->string('site_has_building')->nullable();
            $table->string('permitted_type')->nullable();
            $table->string('is_pharmacy')->nullable();
            $table->integer('num_beds')->nullable();
            $table->integer('rooms')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->integer('project_area')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('area')->nullable();
            $table->string('requirement')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // house_applications
        Schema::create('house_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Personal Details of Applicant
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('house_name')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Marital & Family Details
            $table->string('married')->nullable();
            $table->integer('children_total')->nullable();
            $table->integer('children_male')->nullable();
            $table->integer('children_female')->nullable();
            $table->integer('num_children')->nullable();
            $table->integer('num_male_children')->nullable();
            $table->integer('num_female_children')->nullable();

            // Section 3: Income & Health Details
            $table->string('has_occupation')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->integer('other_income')->nullable();
            $table->string('health_status')->nullable();
            $table->text('daily_treatment_explanation')->nullable();

            // Section 4: Existing Housing Details
            $table->text('accommodation_details')->nullable();
            $table->string('own_place')->nullable();
            $table->string('own_place_details')->nullable();
            $table->string('land_type')->nullable();
            $table->integer('building_area_sq')->nullable();

            // Section 5: Proposed Project Details
            $table->string('intended_house_form')->nullable();
            $table->string('house_type')->nullable();
            $table->string('desired_model')->nullable();
            $table->string('expected_amount')->nullable();
            $table->string('permission')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('office_build_house')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // orphan_care_applications
        Schema::create('orphan_care_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Basic Information of Orphan
            $table->string('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('house_name')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('town')->nullable();
            $table->string('post_office')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Information About Late Father
            $table->string('father_name')->nullable();
            $table->string('father_death_date')->nullable();
            $table->string('father_death_cause')->nullable();
            $table->string('grandfather_name')->nullable();

            // Section 3: Information About Mother
            $table->string('mother_name')->nullable();
            $table->string('mother_alive_status')->nullable();
            $table->string('mother_death_date')->nullable();
            $table->string('mother_death_cause')->nullable();
            $table->string('mother_remarried_status')->nullable();
            $table->string('mothers_father_name')->nullable();

            // Section 4: Family & Livelihood Details
            $table->integer('siblings_male')->nullable();
            $table->integer('siblings_female')->nullable();
            $table->integer('siblings_total')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('health_status')->nullable();
            $table->string('house_type')->nullable();

            // Section 5: Education & Sponsorship
            $table->string('school_name')->nullable();
            $table->string('school_class')->nullable();
            $table->string('madrassa_name')->nullable();
            $table->string('madrassa_class')->nullable();
            $table->string('not_studying_reason')->nullable();
            $table->integer('monthly_expense')->nullable();
            $table->string('sponsorship_details')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });

        // shop_other_applications
        Schema::create('shop_other_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            // Section 1: Organization Details
            $table->string('committee_name')->nullable();
            $table->string('reg_number')->nullable();
            $table->integer('year')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();

            // Section 2: Proposed Locality Details
            $table->string('mahallu_name')->nullable();
            $table->string('locality_pin_code')->nullable();
            $table->string('locality_place')->nullable();
            $table->string('locality_village')->nullable();
            $table->string('locality_post')->nullable();
            $table->string('locality_panchayath')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('families_in_mahallu')->nullable();

            // Section 3: Project Details
            $table->string('site_has_building')->nullable();
            $table->string('status_of_current_building')->nullable();
            $table->string('office_shop')->nullable();
            $table->string('permitted_type')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->integer('project_area')->nullable();
            $table->integer('num_rooms')->nullable();
            $table->integer('rooms')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('area')->nullable();

            // Recommendation Details
            $table->string('recommendation_name')->nullable();
            $table->string('recommendation_organization')->nullable();
            $table->string('recommendation_organization_other')->nullable();
            $table->string('recommendation_phone')->nullable();
            $table->string('recommendation_position')->nullable();

            $table->timestamps();
        });
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
