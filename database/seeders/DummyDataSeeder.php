<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopOtherApplication;
use App\Models\OrphanCareApplication;
use App\Models\HouseApplication;
use App\Models\HospitalClinicApplication;
use App\Models\GeneralApplication;
use App\Models\FamilyAidApplication;
use App\Models\EducationCenterApplication;
use App\Models\DrinkingWaterIndividualApplication;
use App\Models\DrinkingWaterGroupApplication;
use App\Models\DifferentlyAbledApplication;
use App\Models\CulturalCenterApplication;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate to start fresh
        ShopOtherApplication::truncate();
        OrphanCareApplication::truncate();
        HouseApplication::truncate();
        HospitalClinicApplication::truncate();
        GeneralApplication::truncate();
        FamilyAidApplication::truncate();
        EducationCenterApplication::truncate();
        DrinkingWaterIndividualApplication::truncate();
        DrinkingWaterGroupApplication::truncate();
        DifferentlyAbledApplication::truncate();
        CulturalCenterApplication::truncate();

        $metaData = [
            'committee_name' => 'Local Welfare Association',
            'reg_number' => 'REG987654',
            'year' => '2024',
            'contact_number_1' => '9876543210',
            'contact_number_2' => '9876543211',
            'submitted_before' => 'No',
            'received_support_before' => 'No',
            'mahallu_name' => 'Local Mahallu',
            'families_in_mahallu' => '200',
            'requirement' => 'New construction',
            'project_type' => 'Classroom',
            'site_has_building' => 'No',
            'status_of_current_building' => 'None',
            'students_boys' => '30',
            'students_girls' => '30',
            'education_center_nearby' => 'Local Primary School (2 KM)',
            'cultural_center_nearby' => 'Local Cultural Club (2 KM)',
            'syllabus' => 'General Board',
            'building_area_sq' => '1000',
            'land_area_sq' => '2500',
            'num_classrooms' => '2',
            'num_students' => '60',
            'legal_approvals_status' => 'Approved',
            'area' => 'Town Center',
            'permitted_type' => 'Clinic',
            'num_rooms' => '2',
            'office_shop' => 'Shop',
            'num_beds' => 15,
            'age' => '35',
            'gender' => 'Male',
            'father_name' => 'Sreedharan Nair',
            'mother_name' => 'Devaki Amma',
            'house_name' => 'Nair House',
            'location' => 'Town Junction',
            'married' => 'Yes',
            'children_total' => '3',
            'children_male' => '2',
            'children_female' => '1',
            'occupation' => 'Daily wage worker',
            'monthly_income' => '8000',
            'health_status' => 'Good',
            'accommodation_details' => 'Rented house',
            'own_place' => 'Yes',
            'land_type' => 'Homestead',
            'desired_model' => '2 BHK',
            'expected_amount' => '350000',
            'permission' => 'Yes',
            'house_type' => 'Concrete',
            'father_death_date' => '2020-10-12',
            'father_death_cause' => 'Heart Attack',
            'mother_alive_status' => 'Yes',
            'siblings_total' => '2',
            'school_name' => 'Government High School',
            'school_class' => '6th Grade',
            'madrassa_name' => 'Local Madrassa',
            'madrassa_class' => 'Class 4',
            'town' => 'Local Town',
            'disability_type' => 'Physical',
            'disability_percentage' => '75',
            'disability_level' => 'Severe',
            'guardian_name' => 'Hassan Kunhi',
            'guardian_relation' => 'Father',
            'mobile' => '9876543228',
            'monthly_cost' => '1500',
            'income_source' => 'Agriculture',
            'own_house_condition' => 'Semi-concrete',
            'own_place_size' => '5 Cents',
            'own_place_status' => 'Own land'
        ];

        $categories = [
            'Education Center' => [
                'model' => EducationCenterApplication::class,
                'name' => 'Abdurahiman K.P.',
                'details' => 'Requested financial aid to build two new classrooms for primary students.',
                'place' => 'Kozhikode',
                'village' => 'Mavoor',
                'panchayat' => 'Chathamangalam',
                'post_office' => 'Mavoor PO',
            ],
            'Cultural Center' => [
                'model' => CulturalCenterApplication::class,
                'name' => 'Siddique K.T.',
                'details' => 'Requested assistance to build a new community library block.',
                'place' => 'Mukkam',
                'village' => 'Koduvally',
                'panchayat' => 'Kizhakkoth',
                'post_office' => 'Koduvally PO',
            ],
            'Hospital or Clinics' => [
                'model' => HospitalClinicApplication::class,
                'name' => 'Jose Sebastian',
                'details' => 'Proposed medical dispensary building setup for poor residents.',
                'place' => 'Perambra',
                'village' => 'Perambra',
                'panchayat' => 'Kunnummal',
                'post_office' => 'Perambra PO',
            ],
            'Shops and Others' => [
                'model' => ShopOtherApplication::class,
                'name' => 'Zainaba Hassan',
                'details' => 'Setting up a community grocery store to support self-employment.',
                'place' => 'Vatakara',
                'village' => 'Vatakara',
                'panchayat' => 'Kottoor',
                'post_office' => 'Vatakara PO',
            ],
            'House' => [
                'model' => HouseApplication::class,
                'name' => 'Rahul Nair',
                'details' => 'Applying for financial aid to construct a 2 BHK shelter for a homeless family.',
                'place' => 'Thamarassery',
                'village' => 'Thamarassery',
                'panchayat' => 'Nochad',
                'post_office' => 'Thamarassery PO',
            ],
            'Drinking Water - Group Level' => [
                'model' => DrinkingWaterGroupApplication::class,
                'name' => 'Mohammed Faisal',
                'details' => 'Requested boring well installation for a water-scarce colony.',
                'place' => 'Kunnamangalam',
                'village' => 'Kunnamangalam',
                'panchayat' => 'Kayanna',
                'post_office' => 'Kunnamangalam PO',
            ],
            'Drinking Water - Individual Level' => [
                'model' => DrinkingWaterIndividualApplication::class,
                'name' => 'Meera Suresh',
                'details' => 'Requested support for open well construction on family land.',
                'place' => 'Chelannur',
                'village' => 'Chelannur',
                'panchayat' => 'Koorachundu',
                'post_office' => 'Chelannur PO',
            ],
            'General' => [
                'model' => GeneralApplication::class,
                'name' => 'Anas Ibrahim',
                'details' => 'Applying for emergency financial aid for marriage expense support.',
                'place' => 'Balussery',
                'village' => 'Balussery',
                'panchayat' => 'Kattippara',
                'post_office' => 'Balussery PO',
            ],
            'Orphan Care' => [
                'model' => OrphanCareApplication::class,
                'name' => 'Tommy Atkins',
                'details' => 'Financial aid application for monthly sponsorship of two orphans.',
                'place' => 'Feroke',
                'village' => 'Feroke',
                'panchayat' => 'Thiruvambady',
                'post_office' => 'Feroke PO',
            ],
            'Differently Abled' => [
                'model' => DifferentlyAbledApplication::class,
                'name' => 'Fatima Beevi',
                'details' => 'Requested financial aid for wheelchair and medical support.',
                'place' => 'Ramanattukara',
                'village' => 'Ramanattukara',
                'panchayat' => 'Kodenchery',
                'post_office' => 'Ramanattukara PO',
            ],
            'Family Aid' => [
                'model' => FamilyAidApplication::class,
                'name' => 'Meera Suresh',
                'details' => 'Requested family aid for monthly livelihood support.',
                'place' => 'Beypore',
                'village' => 'Beypore',
                'panchayat' => 'Kizhakkoth',
                'post_office' => 'Beypore PO',
            ],
        ];

        foreach ($categories as $categoryName => $config) {
            $modelClass = $config['model'];
            $instance = new $modelClass();
            $metaFields = $instance->metaFields ?? [];

            // Filter metaData to only keys in $metaFields
            $filteredMeta = [];
            foreach ($metaFields as $field) {
                if (isset($metaData[$field])) {
                    $filteredMeta[$field] = $metaData[$field];
                }
            }

            $modelClass::create([
                'applicant_name' => $config['name'],
                'category' => $categoryName,
                'amount_requested' => 150000,
                'status' => 'Pending',
                'details' => $config['details'],
                'place' => $config['place'],
                'post_office' => $config['post_office'],
                'village' => $config['village'],
                'panchayat' => $config['panchayat'],
                'district' => 'Kozhikode',
                'state' => 'Kerala',
                'pin_code' => '673001',
                'meta' => $filteredMeta
            ]);
        }
    }
}
