<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Test Super Admin',
            'email' => 'admin_feature_test@rcfi.org',
            'mobile' => '9999999999',
            'role' => 1,
            'password' => bcrypt('password'),
            'designation' => 'Super Admin',
        ]);
    }

    /**
     * Test submitting an application across all 11 categories succeeds and redirects.
     */
    public function test_submitting_application_for_all_11_categories_succeeds(): void
    {
        $categories = [
            'education-center' => [
                'name' => 'Education Center',
                'table' => 'education_center_applications',
            ],
            'cultural-center' => [
                'name' => 'Cultural Center',
                'table' => 'cultural_center_applications',
            ],
            'hospital-or-clinics' => [
                'name' => 'Hospital or Clinics',
                'table' => 'hospital_clinic_applications',
            ],
            'shops-and-others' => [
                'name' => 'Shops and Others',
                'table' => 'shop_other_applications',
            ],
            'house' => [
                'name' => 'House',
                'table' => 'house_applications',
            ],
            'drinking-water-group-level' => [
                'name' => 'Drinking Water - Group Level',
                'table' => 'drinking_water_group_applications',
            ],
            'drinking-water-individual-level' => [
                'name' => 'Drinking Water - Individual Level',
                'table' => 'drinking_water_individual_applications',
            ],
            'orphan-care' => [
                'name' => 'Orphan Care',
                'table' => 'orphan_care_applications',
            ],
            'differently-abled' => [
                'name' => 'Differently Abled',
                'table' => 'differently_abled_applications',
            ],
            'family-aid' => [
                'name' => 'Family Aid',
                'table' => 'family_aid_applications',
            ],
            'general' => [
                'name' => 'General',
                'table' => 'general_applications',
            ],
        ];

        foreach ($categories as $slug => $config) {
            $applicantName = 'Test Applicant ' . $config['name'];

            $response = $this->actingAs($this->admin)->post('/admin/applications', [
                'category' => $config['name'],
                'redirect_category' => $slug,
                'applicant_name' => $applicantName,
                'amount_requested' => '10000',
                'status' => 'Pending',
                'house_name' => 'Villa ' . $slug,
                'place' => 'Central Place',
                'post_office' => 'Main PO',
                'village' => 'West Village',
                'panchayat' => 'East Panchayat',
                'district' => 'Kozhikode',
                'state' => 'Kerala',
                'pin_code' => '673001',
                'contact_number_1' => '9876543210',
                'meta' => [
                    'gender' => 'Male',
                    'father_name' => 'Father Name',
                    'mother_name' => 'Mother Name',
                    'dob' => '',
                    'age' => '',
                    'siblings_male' => '',
                    'siblings_female' => '',
                    'siblings_total' => '',
                    'monthly_income' => '',
                    'monthly_expense' => '',
                ]
            ]);

            $response->assertRedirect(route('applications.category', $slug));

            $this->assertDatabaseHas($config['table'], [
                'applicant_name' => $applicantName,
            ]);
        }
    }

    /**
     * Test every application table contains shared address columns.
     */
    public function test_all_application_tables_contain_shared_address_columns(): void
    {
        $tables = [
            'house_applications',
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'general_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'orphan_care_applications',
            'family_aid_applications',
            'differently_abled_applications',
        ];

        $requiredColumns = [
            'house_name',
            'place',
            'post_office',
            'village',
            'panchayat',
            'district',
            'state',
            'pin_code',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                Schema::hasColumns($table, $requiredColumns),
                "Table {$table} is missing one or more required address columns: " . implode(', ', $requiredColumns)
            );
        }
    }

    /**
     * Test HasCategoryMeta trait normalizes empty string metadata values to null in database.
     */
    public function test_has_category_meta_normalizes_empty_strings_to_null(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/applications', [
            'category' => 'Orphan Care',
            'redirect_category' => 'orphan-care',
            'applicant_name' => 'Null Normalization Test Orphan',
            'amount_requested' => '0',
            'status' => 'Pending',
            'meta' => [
                'gender' => 'Female',
                'father_name' => 'Late Father',
                'dob' => '',
                'father_death_date' => '',
                'siblings_male' => '',
                'siblings_female' => '',
                'siblings_total' => '',
                'monthly_income' => '',
                'monthly_expense' => '',
            ]
        ]);

        $response->assertRedirect(route('applications.category', 'orphan-care'));

        $record = \App\Models\OrphanCareApplication::where('applicant_name', 'Null Normalization Test Orphan')->first();

        $this->assertNotNull($record, 'Orphan Care record was not saved to database.');
        $this->assertNull($record->siblings_male, 'siblings_male should be null, not empty string.');
        $this->assertNull($record->siblings_female, 'siblings_female should be null, not empty string.');
        $this->assertNull($record->father_death_date, 'father_death_date should be null, not empty string.');
    }
}
