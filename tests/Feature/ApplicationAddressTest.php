<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\HouseApplication;
use App\Models\EducationCenterApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_house_application_with_address_fields(): void
    {
        // 1. Get or create a super admin user
        $admin = User::where('role', 1)->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin Test',
                'email' => 'admin_test@rcfi.org',
                'mobile' => '9999999999',
                'role' => 1,
                'password' => bcrypt('password'),
                'designation' => 'Super Admin',
            ]);
        }

        // 2. Act as admin and post data
        $response = $this->actingAs($admin)->post('/admin/applications', [
            'category' => 'House',
            'applicant_name' => 'John Doe House Test',
            'amount_requested' => 300000,
            'status' => 'Pending',
            'contact_email' => 'john@house.com',
            'details' => 'Address validation check',
            'house_name' => 'Happy Villa',
            'place' => 'Downtown',
            'post_office' => 'PO City',
            'village' => 'Green Village',
            'panchayat' => 'GP East',
            'district' => 'Kozhikode',
            'state' => 'Kerala',
            'pin_code' => '673001',
            'redirect_all' => '1',
            'redirect_category' => 'house',
        ]);

        // 3. Assert redirect and DB entry
        $response->assertRedirect(route('applications.category', 'house'));
        
        $this->assertDatabaseHas('house_applications', [
            'applicant_name' => 'John Doe House Test',
        ]);

        $this->assertDatabaseHas('applicant_addresses', [
            'addressable_type' => HouseApplication::class,
            'house_name' => 'Happy Villa',
            'place' => 'Downtown',
            'post_office' => 'PO City',
            'village' => 'Green Village',
            'panchayat' => 'GP East',
            'district' => 'Kozhikode',
            'state' => 'Kerala',
            'pin_code' => '673001',
        ]);
    }

    public function test_super_admin_can_create_group_application_without_house_name(): void
    {
        $admin = User::where('role', 1)->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin Test',
                'email' => 'admin_test@rcfi.org',
                'mobile' => '9999999999',
                'role' => 1,
                'password' => bcrypt('password'),
                'designation' => 'Super Admin',
            ]);
        }

        $response = $this->actingAs($admin)->post('/admin/applications', [
            'category' => 'Education Center',
            'applicant_name' => 'Group Edu Test',
            'amount_requested' => 500000,
            'status' => 'Pending',
            'contact_email' => 'group@edu.com',
            'details' => 'Group address validation check',
            'place' => 'Uptown',
            'post_office' => 'PO North',
            'village' => 'Blue Village',
            'panchayat' => 'GP West',
            'district' => 'Ernakulam',
            'state' => 'Kerala',
            'pin_code' => '682001',
            'redirect_all' => '1',
            'redirect_category' => 'education-center',
        ]);

        $response->assertRedirect(route('applications.category', 'education-center'));

        $this->assertDatabaseHas('education_center_applications', [
            'applicant_name' => 'Group Edu Test',
        ]);

        $this->assertDatabaseHas('applicant_addresses', [
            'addressable_type' => EducationCenterApplication::class,
            'place' => 'Uptown',
            'post_office' => 'PO North',
            'village' => 'Blue Village',
            'panchayat' => 'GP West',
            'district' => 'Ernakulam',
            'state' => 'Kerala',
            'pin_code' => '682001',
        ]);
    }

    public function test_super_admin_can_create_orphan_care_application_with_address_and_mobile_numbers(): void
    {
        $admin = User::where('role', 1)->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin Test',
                'email' => 'admin_test@rcfi.org',
                'mobile' => '9999999999',
                'role' => 1,
                'password' => bcrypt('password'),
                'designation' => 'Super Admin',
            ]);
        }

        $response = $this->actingAs($admin)->post('/admin/applications', [
            'category' => 'Orphan Care',
            'applicant_name' => 'Orphan Test Applicant',
            'status' => 'Pending',
            'house_name' => 'Rose Villa',
            'place' => 'Calicut',
            'post_office' => 'Calicut PO',
            'village' => 'East Village',
            'panchayat' => 'Calicut GP',
            'district' => 'Kozhikode',
            'state' => 'Kerala',
            'pin_code' => '673002',
            'meta' => [
                'mobile_1' => '9876543210',
                'mobile_2' => '9123456789',
                'father_name' => 'John Doe Sr',
                'mother_name' => 'Jane Doe',
                'gender' => 'Male',
                'dob' => '2005-05-15',
                'age' => '21',
                'father_death_date' => '2018-01-01',
                'father_death_cause' => 'Fever',
                'mother_alive_status' => 'Yes',
                'mother_remarried_status' => 'No',
                'siblings_total' => 2,
                'siblings_male' => 1,
                'siblings_female' => 1,
                'monthly_income' => 5000,
                'monthly_expense' => 4000,
                'house_type' => 'Rental',
                'school_name' => 'City School',
                'school_class' => '10',
                'health_status' => 'Good',
                'guardian_name' => 'Jane Doe',
                'guardian_relation' => 'Mother',
            ],
            'redirect_category' => 'orphan-care',
        ]);

        $response->assertRedirect(route('applications.category', 'orphan-care'));

        $app = \App\Models\OrphanCareApplication::where('applicant_name', 'Orphan Test Applicant')->first();
        $this->assertNotNull($app);
        $this->assertEquals('9876543210', $app->meta['mobile_1']);
        $this->assertEquals('9123456789', $app->meta['mobile_2']);
        $this->assertEquals('Rose Villa', $app->meta['house_name']);
        $this->assertEquals('Calicut', $app->meta['place']);
        $this->assertEquals('Kozhikode', $app->meta['district']);

        $this->assertDatabaseHas('applicant_addresses', [
            'addressable_type' => \App\Models\OrphanCareApplication::class,
            'addressable_id' => $app->id,
            'house_name' => 'Rose Villa',
            'place' => 'Calicut',
            'contact_number_1' => '9876543210',
            'contact_number_2' => '9123456789',
        ]);
    }
}
