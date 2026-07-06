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
        // 1. Create all 11 new category tables with explicit columns
        // cultural_center_applications
        Schema::create('cultural_center_applications', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->string('category');
            $table->integer('amount_requested')->nullable();
            $table->string('status')->default('Pending');
            $table->string('contact_email')->nullable();
            $table->text('details')->nullable();

            $table->string('area')->nullable();
            $table->integer('area_sqft')->nullable();
            $table->string('benefited_households')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->string('committee_name')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('cultural_center_nearby')->nullable();
            $table->string('distance_cultural_centre')->nullable();
            $table->string('district')->nullable();
            $table->string('estimated_amount')->nullable();
            $table->integer('families_in_mahallu')->nullable();
            $table->integer('land_area_sq')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_location')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('locality_village')->nullable();
            $table->string('location')->nullable();
            $table->string('mahallu_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('num_beneficiaries')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('place')->nullable();
            $table->string('post')->nullable();
            $table->string('project_type')->nullable();
            $table->string('received_support_before')->nullable();
            $table->string('reg_number')->unique()->nullable();
            $table->string('requirement')->nullable();
            $table->integer('rooms')->nullable();
            $table->string('site_has_building')->nullable();
            $table->string('state')->nullable();
            $table->string('status_of_current_building')->nullable();
            $table->string('submitted_before')->nullable();
            $table->integer('village')->nullable();
            $table->integer('year')->nullable();

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

            $table->string('aadhar_number')->nullable();
            $table->string('accommodation')->nullable();
            $table->integer('age')->nullable();
            $table->string('description')->nullable();
            $table->string('disability_date')->nullable();
            $table->string('disability_level')->nullable();
            $table->integer('disability_percentage')->nullable();
            $table->string('disability_type')->nullable();
            $table->string('district')->nullable();
            $table->string('dob')->nullable();
            $table->string('father_name')->nullable();
            $table->string('fathers_father')->nullable();
            $table->integer('female_members')->nullable();
            $table->string('gender')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('health_status')->nullable();
            $table->string('house_name')->nullable();
            $table->integer('income_source')->nullable();
            $table->integer('male_members')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('mobile')->nullable();
            $table->integer('monthly_cost')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('not_studying_reason')->nullable();
            $table->string('other_help')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('people_with_disabilities')->nullable();
            $table->string('pincode')->nullable();
            $table->string('place')->nullable();
            $table->string('studying_institution')->nullable();
            $table->integer('total_members')->nullable();

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

            $table->string('aadhar_number')->nullable();
            $table->text('address')->nullable();
            $table->integer('age')->nullable();
            $table->json('beneficiaries')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('district')->nullable();
            $table->string('dob')->nullable();
            $table->string('father_name')->nullable();
            $table->string('fathers_father')->nullable();
            $table->integer('female_adults')->nullable();
            $table->integer('female_children')->nullable();
            $table->string('gender')->nullable();
            $table->text('land_owner_address')->nullable();
            $table->string('land_owner_district')->nullable();
            $table->string('land_owner_mobile')->nullable();
            $table->string('land_owner_name')->nullable();
            $table->string('land_owner_panchayath')->nullable();
            $table->string('land_owner_place')->nullable();
            $table->string('land_owner_post')->nullable();
            $table->string('legal_permissions')->nullable();
            $table->string('location')->nullable();
            $table->integer('male_adults')->nullable();
            $table->integer('male_children')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('num_benefited_people')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('pin')->nullable();
            $table->string('post')->nullable();
            $table->string('state')->nullable();
            $table->integer('village')->nullable();
            $table->integer('well_depth')->nullable();
            $table->string('well_type')->nullable();

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

            $table->string('aadhar_number')->nullable();
            $table->text('address')->nullable();
            $table->integer('age')->nullable();
            $table->json('beneficiaries')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('current_water_source')->nullable();
            $table->string('district')->nullable();
            $table->string('dob')->nullable();
            $table->string('father_name')->nullable();
            $table->string('fathers_father')->nullable();
            $table->string('gender')->nullable();
            $table->string('job')->nullable();
            $table->string('land_nature')->nullable();
            $table->text('land_owner_address')->nullable();
            $table->string('land_owner_district')->nullable();
            $table->string('land_owner_mobile')->nullable();
            $table->string('land_owner_name')->nullable();
            $table->string('land_owner_panchayath')->nullable();
            $table->string('land_owner_place')->nullable();
            $table->string('land_owner_post')->nullable();
            $table->string('legal_permissions')->nullable();
            $table->string('location')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('need_pump')->nullable();
            $table->string('num_benefited_people')->nullable();
            $table->string('num_female_benefited')->nullable();
            $table->string('num_male_benefited')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('pin')->nullable();
            $table->string('post')->nullable();
            $table->string('state')->nullable();
            $table->integer('village')->nullable();
            $table->integer('well_depth')->nullable();
            $table->string('well_diameter')->nullable();
            $table->string('well_for_agriculture')->nullable();
            $table->string('well_type')->nullable();

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

            $table->string('area')->nullable();
            $table->integer('area_sqft')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->string('committee_name')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('distance_cultural_centre')->nullable();
            $table->string('district')->nullable();
            $table->string('education_center_nearby')->nullable();
            $table->string('estimated_amount')->nullable();
            $table->integer('families_in_mahallu')->nullable();
            $table->integer('land_area_sq')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_location')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('locality_village')->nullable();
            $table->string('location')->nullable();
            $table->string('mahallu_name')->nullable();
            $table->string('mobile')->nullable();
            $table->integer('num_classrooms')->nullable();
            $table->integer('num_students')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('place')->nullable();
            $table->string('post')->nullable();
            $table->string('project_type')->nullable();
            $table->string('received_support_before')->nullable();
            $table->string('reg_number')->unique()->nullable();
            $table->string('requirement')->nullable();
            $table->integer('rooms')->nullable();
            $table->string('site_has_building')->nullable();
            $table->string('state')->nullable();
            $table->string('status_of_current_building')->nullable();
            $table->integer('students_boys')->nullable();
            $table->integer('students_girls')->nullable();
            $table->string('submitted_before')->nullable();
            $table->string('syllabus')->nullable();
            $table->integer('village')->nullable();
            $table->integer('year')->nullable();

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

            $table->string('aadhar_number')->nullable();
            $table->integer('age')->nullable();
            $table->integer('children_female')->nullable();
            $table->integer('children_male')->nullable();
            $table->integer('children_total')->nullable();
            $table->text('chronic_patients_description')->nullable();
            $table->string('disability_status')->nullable();
            $table->string('district')->nullable();
            $table->string('dob')->nullable();
            $table->string('father_name')->nullable();
            $table->string('fathers_father')->nullable();
            $table->string('health_status')->nullable();
            $table->string('house_name')->nullable();
            $table->string('location')->nullable();
            $table->string('mobile_1')->nullable();
            $table->string('mobile_2')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nri_status')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('other_income_sources')->nullable();
            $table->string('own_house_condition')->nullable();
            $table->string('own_place_size')->nullable();
            $table->string('own_place_status')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('post_office')->nullable();
            $table->string('residence_info')->nullable();
            $table->text('routine_treatment_explanation')->nullable();
            $table->string('sequel_status')->nullable();
            $table->integer('welfare_assistance_areas')->nullable();

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

            $table->text('accommodation_details')->nullable();
            $table->text('address')->nullable();
            $table->integer('age')->nullable();
            $table->string('applying_for')->nullable();
            $table->integer('average_monthly_income')->nullable();
            $table->string('block')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('district')->nullable();
            $table->string('education')->nullable();
            $table->string('expected_amount')->nullable();
            $table->string('father_name')->nullable();
            $table->integer('female_family_members')->nullable();
            $table->string('general_app_status')->nullable();
            $table->string('health_status')->nullable();
            $table->string('house_name')->nullable();
            $table->string('location')->nullable();
            $table->integer('male_family_members')->nullable();
            $table->string('married')->nullable();
            $table->string('mobile_1')->nullable();
            $table->string('mobile_2')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->integer('monthly_income_detail')->nullable();
            $table->string('mother_name')->nullable();
            $table->integer('num_earning_members')->nullable();
            $table->string('num_female_family')->nullable();
            $table->string('num_male_family')->nullable();
            $table->string('num_total_family')->nullable();
            $table->string('occupation')->nullable();
            $table->string('office_app_type')->nullable();
            $table->string('office_application_type')->nullable();
            $table->integer('other_income')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('panchayat_municipality_corporation')->nullable();
            $table->string('pin')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('post')->nullable();
            $table->string('post_office')->nullable();
            $table->string('recommended_by')->nullable();
            $table->string('recommended_phone')->nullable();
            $table->string('sex')->nullable();
            $table->string('state')->nullable();
            $table->string('status_of_applicant')->nullable();
            $table->integer('total_family_members')->nullable();
            $table->integer('village')->nullable();
            $table->string('ward')->nullable();

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

            $table->string('area')->nullable();
            $table->integer('area_sqft')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->string('committee_name')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('district')->nullable();
            $table->string('estimated_amount')->nullable();
            $table->integer('families_in_mahallu')->nullable();
            $table->string('is_pharmacy')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('locality_location')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('locality_village')->nullable();
            $table->string('mahallu_name')->nullable();
            $table->string('mobile')->nullable();
            $table->integer('num_beds')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('permitted_type')->nullable();
            $table->string('place')->nullable();
            $table->string('post')->nullable();
            $table->integer('project_area')->nullable();
            $table->string('reg_number')->unique()->nullable();
            $table->string('requirement')->nullable();
            $table->integer('rooms')->nullable();
            $table->string('site_has_building')->nullable();
            $table->string('state')->nullable();
            $table->integer('village')->nullable();
            $table->integer('year')->nullable();

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

            $table->text('accommodation_details')->nullable();
            $table->integer('age')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->integer('children_female')->nullable();
            $table->integer('children_male')->nullable();
            $table->integer('children_total')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->text('daily_treatment_explanation')->nullable();
            $table->string('desired_model')->nullable();
            $table->string('district')->nullable();
            $table->string('education')->nullable();
            $table->string('expected_amount')->nullable();
            $table->string('father_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('has_occupation')->nullable();
            $table->string('health_status')->nullable();
            $table->string('house_name')->nullable();
            $table->string('house_type')->nullable();
            $table->string('intended_house_form')->nullable();
            $table->string('land_type')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('location')->nullable();
            $table->string('married')->nullable();
            $table->string('mobile_1')->nullable();
            $table->string('mobile_2')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('mother_name')->nullable();
            $table->integer('num_children')->nullable();
            $table->integer('num_female_children')->nullable();
            $table->integer('num_male_children')->nullable();
            $table->string('occupation')->nullable();
            $table->string('office_build_house')->nullable();
            $table->integer('other_income')->nullable();
            $table->string('own_place')->nullable();
            $table->text('own_place_details')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('permission')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('post')->nullable();
            $table->string('post_office')->nullable();
            $table->string('state')->nullable();

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

            $table->string('aadhar_number')->nullable();
            $table->integer('age')->nullable();
            $table->string('district')->nullable();
            $table->string('dob')->nullable();
            $table->string('father_death_cause')->nullable();
            $table->string('father_death_date')->nullable();
            $table->string('father_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('health_status')->nullable();
            $table->string('house_name')->nullable();
            $table->string('house_type')->nullable();
            $table->string('madrassa_class')->nullable();
            $table->string('madrassa_name')->nullable();
            $table->string('mobile_1')->nullable();
            $table->string('mobile_2')->nullable();
            $table->integer('monthly_expense')->nullable();
            $table->integer('monthly_income')->nullable();
            $table->string('mother_alive_status')->nullable();
            $table->string('mother_death_cause')->nullable();
            $table->string('mother_death_date')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_remarried_status')->nullable();
            $table->string('mothers_father_name')->nullable();
            $table->string('not_studying_reason')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('place')->nullable();
            $table->string('post_office')->nullable();
            $table->string('school_class')->nullable();
            $table->string('school_name')->nullable();
            $table->string('siblings_female')->nullable();
            $table->string('siblings_male')->nullable();
            $table->string('siblings_total')->nullable();
            $table->text('sponsorship_details')->nullable();
            $table->string('state')->nullable();
            $table->string('town')->nullable();

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

            $table->string('area')->nullable();
            $table->integer('area_sqft')->nullable();
            $table->integer('building_area_sq')->nullable();
            $table->string('committee_name')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('district')->nullable();
            $table->string('estimated_amount')->nullable();
            $table->integer('families_in_mahallu')->nullable();
            $table->string('legal_approvals_status')->nullable();
            $table->string('locality_district')->nullable();
            $table->string('locality_location')->nullable();
            $table->string('locality_state')->nullable();
            $table->integer('locality_village')->nullable();
            $table->string('mahallu_name')->nullable();
            $table->string('mobile')->nullable();
            $table->integer('num_rooms')->nullable();
            $table->string('office_shop')->nullable();
            $table->string('panchayat')->nullable();
            $table->string('panchayath')->nullable();
            $table->string('permitted_type')->nullable();
            $table->string('place')->nullable();
            $table->string('post')->nullable();
            $table->integer('project_area')->nullable();
            $table->string('reg_number')->unique()->nullable();
            $table->integer('rooms')->nullable();
            $table->string('site_has_building')->nullable();
            $table->string('state')->nullable();
            $table->string('status_of_current_building')->nullable();
            $table->integer('village')->nullable();
            $table->integer('year')->nullable();

            $table->timestamps();
        });


        // 2. Migrate existing data from old table to new tables
        if (Schema::hasTable('applications')) {
            $existing = DB::table('applications')->get();
            foreach ($existing as $app) {
                $targetTable = $this->tables[$app->category] ?? null;
                if ($targetTable) {
                    $insertData = [
                        'id' => $app->id,
                        'applicant_name' => $app->applicant_name,
                        'category' => $app->category,
                        'amount_requested' => $app->amount_requested,
                        'status' => $app->status,
                        'contact_email' => $app->contact_email,
                        'details' => $app->details,
                        'created_at' => $app->created_at,
                        'updated_at' => $app->updated_at,
                    ];

                    if (!empty($app->meta)) {
                        $meta = is_string($app->meta) ? json_decode($app->meta, true) : (array)$app->meta;
                        if (is_array($meta)) {
                            foreach ($meta as $key => $val) {
                                if ($key === 'beneficiaries') {
                                    $insertData[$key] = is_array($val) ? json_encode($val) : $val;
                                } else {
                                    $insertData[$key] = $val;
                                }
                            }
                        }
                    }

                    DB::table($targetTable)->insert($insertData);
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
        $categoryFields = [
            'cultural_center_applications' => ['area', 'area_sqft', 'benefited_households', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'cultural_center_nearby', 'distance_cultural_centre', 'district', 'estimated_amount', 'families_in_mahallu', 'land_area_sq', 'legal_approvals_status', 'locality_district', 'locality_location', 'locality_state', 'locality_village', 'location', 'mahallu_name', 'mobile', 'num_beneficiaries', 'panchayat', 'panchayath', 'place', 'post', 'project_type', 'received_support_before', 'reg_number', 'requirement', 'rooms', 'site_has_building', 'state', 'status_of_current_building', 'submitted_before', 'village', 'year'],
            'differently_abled_applications' => ['aadhar_number', 'accommodation', 'age', 'description', 'disability_date', 'disability_level', 'disability_percentage', 'disability_type', 'district', 'dob', 'father_name', 'fathers_father', 'female_members', 'gender', 'guardian_name', 'guardian_relation', 'health_status', 'house_name', 'income_source', 'male_members', 'marital_status', 'mobile', 'monthly_cost', 'monthly_income', 'mother_name', 'not_studying_reason', 'other_help', 'panchayat', 'people_with_disabilities', 'pincode', 'place', 'studying_institution', 'total_members'],
            'drinking_water_group_applications' => ['aadhar_number', 'address', 'age', 'beneficiaries', 'contact_number_1', 'contact_number_2', 'district', 'dob', 'father_name', 'fathers_father', 'female_adults', 'female_children', 'gender', 'land_owner_address', 'land_owner_district', 'land_owner_mobile', 'land_owner_name', 'land_owner_panchayath', 'land_owner_place', 'land_owner_post', 'legal_permissions', 'location', 'male_adults', 'male_children', 'mother_name', 'num_benefited_people', 'panchayath', 'pin', 'post', 'state', 'village', 'well_depth', 'well_type'],
            'drinking_water_individual_applications' => ['aadhar_number', 'address', 'age', 'beneficiaries', 'contact_number_1', 'contact_number_2', 'current_water_source', 'district', 'dob', 'father_name', 'fathers_father', 'gender', 'job', 'land_nature', 'land_owner_address', 'land_owner_district', 'land_owner_mobile', 'land_owner_name', 'land_owner_panchayath', 'land_owner_place', 'land_owner_post', 'legal_permissions', 'location', 'monthly_income', 'mother_name', 'need_pump', 'num_benefited_people', 'num_female_benefited', 'num_male_benefited', 'panchayath', 'pin', 'post', 'state', 'village', 'well_depth', 'well_diameter', 'well_for_agriculture', 'well_type'],
            'education_center_applications' => ['area', 'area_sqft', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'distance_cultural_centre', 'district', 'education_center_nearby', 'estimated_amount', 'families_in_mahallu', 'land_area_sq', 'legal_approvals_status', 'locality_district', 'locality_location', 'locality_state', 'locality_village', 'location', 'mahallu_name', 'mobile', 'num_classrooms', 'num_students', 'panchayat', 'panchayath', 'place', 'post', 'project_type', 'received_support_before', 'reg_number', 'requirement', 'rooms', 'site_has_building', 'state', 'status_of_current_building', 'students_boys', 'students_girls', 'submitted_before', 'syllabus', 'village', 'year'],
            'family_aid_applications' => ['aadhar_number', 'age', 'children_female', 'children_male', 'children_total', 'chronic_patients_description', 'disability_status', 'district', 'dob', 'father_name', 'fathers_father', 'health_status', 'house_name', 'location', 'mobile_1', 'mobile_2', 'monthly_income', 'mother_name', 'nri_status', 'occupation', 'other_income_sources', 'own_house_condition', 'own_place_size', 'own_place_status', 'panchayat', 'pin_code', 'post_office', 'residence_info', 'routine_treatment_explanation', 'sequel_status', 'welfare_assistance_areas'],
            'general_applications' => ['accommodation_details', 'address', 'age', 'applying_for', 'average_monthly_income', 'block', 'contact_number_1', 'contact_number_2', 'district', 'education', 'expected_amount', 'father_name', 'female_family_members', 'general_app_status', 'health_status', 'house_name', 'location', 'male_family_members', 'married', 'mobile_1', 'mobile_2', 'monthly_income', 'monthly_income_detail', 'mother_name', 'num_earning_members', 'num_female_family', 'num_male_family', 'num_total_family', 'occupation', 'office_app_type', 'office_application_type', 'other_income', 'panchayat', 'panchayat_municipality_corporation', 'pin', 'pin_code', 'post', 'post_office', 'recommended_by', 'recommended_phone', 'sex', 'state', 'status_of_applicant', 'total_family_members', 'village', 'ward'],
            'hospital_clinic_applications' => ['area', 'area_sqft', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'district', 'estimated_amount', 'families_in_mahallu', 'is_pharmacy', 'legal_approvals_status', 'locality_location', 'locality_state', 'locality_village', 'mahallu_name', 'mobile', 'num_beds', 'panchayat', 'panchayath', 'permitted_type', 'place', 'post', 'project_area', 'reg_number', 'requirement', 'rooms', 'site_has_building', 'state', 'village', 'year'],
            'house_applications' => ['accommodation_details', 'age', 'building_area_sq', 'children_female', 'children_male', 'children_total', 'contact_number_1', 'contact_number_2', 'daily_treatment_explanation', 'desired_model', 'district', 'education', 'expected_amount', 'father_name', 'gender', 'has_occupation', 'health_status', 'house_name', 'house_type', 'intended_house_form', 'land_type', 'legal_approvals_status', 'location', 'married', 'mobile_1', 'mobile_2', 'monthly_income', 'mother_name', 'num_children', 'num_female_children', 'num_male_children', 'occupation', 'office_build_house', 'other_income', 'own_place', 'own_place_details', 'panchayat', 'panchayath', 'permission', 'pin_code', 'place', 'post', 'post_office', 'state'],
            'orphan_care_applications' => ['aadhar_number', 'age', 'district', 'dob', 'father_death_cause', 'father_death_date', 'father_name', 'gender', 'grandfather_name', 'guardian_name', 'guardian_relation', 'health_status', 'house_name', 'house_type', 'madrassa_class', 'madrassa_name', 'mobile_1', 'mobile_2', 'monthly_expense', 'monthly_income', 'mother_alive_status', 'mother_death_cause', 'mother_death_date', 'mother_name', 'mother_remarried_status', 'mothers_father_name', 'not_studying_reason', 'pin_code', 'place', 'post_office', 'school_class', 'school_name', 'siblings_female', 'siblings_male', 'siblings_total', 'sponsorship_details', 'state', 'town'],
            'shop_other_applications' => ['area', 'area_sqft', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'district', 'estimated_amount', 'families_in_mahallu', 'legal_approvals_status', 'locality_district', 'locality_location', 'locality_state', 'locality_village', 'mahallu_name', 'mobile', 'num_rooms', 'office_shop', 'panchayat', 'panchayath', 'permitted_type', 'place', 'post', 'project_area', 'reg_number', 'rooms', 'site_has_building', 'state', 'status_of_current_building', 'village', 'year'],
        ];

        foreach ($this->tables as $categoryName => $tableName) {
            if (Schema::hasTable($tableName)) {
                $records = DB::table($tableName)->get();
                foreach ($records as $app) {
                    $meta = [];
                    $fields = $categoryFields[$tableName] ?? [];
                    foreach ($fields as $field) {
                        if (property_exists($app, $field) || isset($app->$field)) {
                            $val = $app->$field;
                            if ($field === 'beneficiaries' && is_string($val)) {
                                $meta[$field] = json_decode($val, true);
                            } else {
                                $meta[$field] = $val;
                            }
                        }
                    }

                    DB::table('applications')->insert([
                        'id' => $app->id,
                        'applicant_name' => $app->applicant_name,
                        'category' => $app->category,
                        'amount_requested' => $app->amount_requested,
                        'status' => $app->status,
                        'contact_email' => $app->contact_email,
                        'details' => $app->details,
                        'meta' => json_encode($meta),
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
