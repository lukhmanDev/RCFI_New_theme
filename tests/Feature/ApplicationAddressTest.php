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
        $response->assertRedirect(route('applications.all'));
        
        $this->assertDatabaseHas('house_applications', [
            'applicant_name' => 'John Doe House Test',
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

        $response->assertRedirect(route('applications.all'));

        $this->assertDatabaseHas('education_center_applications', [
            'applicant_name' => 'Group Edu Test',
            'place' => 'Uptown',
            'post_office' => 'PO North',
            'village' => 'Blue Village',
            'panchayat' => 'GP West',
            'district' => 'Ernakulam',
            'state' => 'Kerala',
            'pin_code' => '682001',
        ]);
    }
}
