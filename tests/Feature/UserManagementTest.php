<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_edit_form_hides_password_field(): void
    {
        $admin = User::factory()->create([
            'role' => 1,
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'role' => 5,
            'email' => 'staff@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
        $response->assertDontSee('Password (Leave blank to keep current)');
    }

    public function test_super_admin_can_retrieve_user_details_via_ajax_endpoint(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 3, 'name' => 'John PM']);
        $staff->profile()->create(['address' => 'My home address']);

        // Create a dummy project assigned to $staff using HouseProject
        \App\Models\HouseProject::create([
            'type_of_project' => 'House',
            'project_manager_id' => $staff->id,
            'status' => 'In Progress',
        ]);

        $response = $this->actingAs($admin)->get("/admin/users/{$staff->id}/details");

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('user.name', 'John PM');
        $response->assertJsonPath('user.address', 'My home address');
        $response->assertJsonCount(1, 'projects');
        $response->assertJsonPath('projects.0.role', 'Project Manager');
    }

    public function test_super_admin_can_toggle_user_suspension(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 3, 'is_suspended' => false]);

        $response = $this->actingAs($admin)->post("/admin/users/{$staff->id}/toggle-suspend");

        $response->assertRedirect();
        $this->assertTrue($staff->fresh()->is_suspended);

        $response = $this->actingAs($admin)->post("/admin/users/{$staff->id}/toggle-suspend");
        $response->assertRedirect();
        $this->assertFalse($staff->fresh()->is_suspended);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => bcrypt('password123'),
            'is_suspended' => true,
        ]);

        $response = $this->post('/doAdminLogin', [
            'email' => 'suspended@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_coo_cannot_change_user_role_during_update(): void
    {
        $coo = User::factory()->create(['role' => 2]);
        $hod = User::factory()->create(['role' => 4]);
        $staff = User::factory()->create(['role' => 3, 'name' => 'Original Name', 'assigned_hod_id' => $hod->id]);

        $response = $this->actingAs($coo)->put("/admin/users/{$staff->id}", [
            'name'            => 'Updated Name',
            'email'           => $staff->email,
            'mobile'          => '9876543210',
            'father_name'     => 'Father Name',
            'mother_name'     => 'Mother Name',
            'date_of_birth'   => '1990-01-01',
            'date_of_joining' => '2020-06-01',
            'gender'          => 'Male',
            'marital_status'  => 'Single',
            'house_name'      => 'Test House',
            'place'           => 'Test Place',
            'po'              => 'Test PO',
            'district'        => 'Test District',
            'state'           => 'Kerala',
            'pin_code'        => '680001',
            'aadhar_number'   => '123456789012',
            'pan_card_number' => 'ABCDE1234F',
            'account_number'  => '1234567890',
            'bank_name'       => 'SBI',
            'bank_branch'     => 'Main Branch',
            'ifsc_code'       => 'SBIN0001234',
            'designation'     => $staff->designation ?? 'Engineer',
            'role'            => 4,
            'assigned_hod_id' => $hod->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Name', $staff->fresh()->name);
        $this->assertEquals('project_manager', $staff->fresh()->role);
    }

    public function test_non_super_admin_cannot_add_staff(): void
    {
        $coo = User::factory()->create(['role' => 2]);

        $response = $this->actingAs($coo)->post('/doAddUser', [
            'name'            => 'New Staff',
            'email'           => 'newstaff@example.com',
            'mobile'          => '9876543210',
            'father_name'     => 'Father Name',
            'mother_name'     => 'Mother Name',
            'date_of_birth'   => '1990-01-01',
            'date_of_joining' => '2020-06-01',
            'gender'          => 'Male',
            'marital_status'  => 'Single',
            'house_name'      => 'Test House',
            'place'           => 'Test Place',
            'po'              => 'Test PO',
            'district'        => 'Test District',
            'state'           => 'Kerala',
            'pin_code'        => '680001',
            'aadhar_number'   => '123456789012',
            'pan_card_number' => 'ABCDE1234F',
            'account_number'  => '1234567890',
            'bank_name'       => 'SBI',
            'bank_branch'     => 'Main Branch',
            'ifsc_code'       => 'SBIN0001234',
            'designation'     => 'Staff',
            'password'        => 'password123',
            'role'            => 2,
        ]);

        $response->assertSessionHasErrors();
        $this->assertNull(User::where('email', 'newstaff@example.com')->first());
    }

    public function test_coo_cannot_change_user_designation_during_update(): void
    {
        $coo = User::factory()->create(['role' => 2]);
        $hod = User::factory()->create(['role' => 4]);
        $staff = User::factory()->create(['role' => 3, 'designation' => 'Original Designation', 'assigned_hod_id' => $hod->id]);

        $response = $this->actingAs($coo)->put("/admin/users/{$staff->id}", [
            'name'            => $staff->name,
            'email'           => $staff->email,
            'mobile'          => '9876543210',
            'father_name'     => 'Father Name',
            'mother_name'     => 'Mother Name',
            'date_of_birth'   => '1990-01-01',
            'date_of_joining' => '2020-06-01',
            'gender'          => 'Male',
            'marital_status'  => 'Single',
            'house_name'      => 'Test House',
            'place'           => 'Test Place',
            'po'              => 'Test PO',
            'district'        => 'Test District',
            'state'           => 'Kerala',
            'pin_code'        => '680001',
            'aadhar_number'   => '123456789012',
            'pan_card_number' => 'ABCDE1234F',
            'account_number'  => '1234567890',
            'bank_name'       => 'SBI',
            'bank_branch'     => 'Main Branch',
            'ifsc_code'       => 'SBIN0001234',
            'designation'     => 'Updated Designation',
            'assigned_hod_id' => $hod->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Original Designation', $staff->fresh()->designation);
    }

    public function test_suspended_logged_in_user_redirected_to_login_on_next_request(): void
    {
        $user = User::factory()->create([
            'email' => 'active@example.com',
            'is_suspended' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertOk();

        // Suspend user mid-session
        $user->update(['is_suspended' => true]);

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_user_logout_redirects_to_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_hod_assignment_requirement_validation_by_role(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $hod = User::factory()->create(['role' => 4, 'name' => 'Department HOD']);

        $baseData = [
            'name'            => 'Test Engineer',
            'email'           => 'engineer@example.com',
            'mobile'          => '9876543210',
            'father_name'     => 'Father Name',
            'mother_name'     => 'Mother Name',
            'date_of_birth'   => '1990-01-01',
            'date_of_joining' => '2020-06-01',
            'gender'          => 'Male',
            'marital_status'  => 'Single',
            'house_name'      => 'Test House',
            'place'           => 'Test Place',
            'po'              => 'Test PO',
            'district'        => 'Test District',
            'state'           => 'Kerala',
            'pin_code'        => '680001',
            'aadhar_number'   => '123456789012',
            'pan_card_number' => 'ABCDE1234F',
            'account_number'  => '1234567890',
            'bank_name'       => 'SBI',
            'bank_branch'     => 'Main Branch',
            'ifsc_code'       => 'SBIN0001234',
            'designation'     => 'Engineer',
            'password'        => 'password123',
            'role'            => 'engineer',
        ];

        // 1. Subordinate staff without assigned_hod_id should fail validation
        $response = $this->actingAs($admin)->post('/doAddUser', array_merge($baseData, ['assigned_hod_id' => '']));
        $response->assertSessionHasErrors('assigned_hod_id');

        // 2. Subordinate staff with assigned_hod_id should succeed
        $response = $this->actingAs($admin)->post('/doAddUser', array_merge($baseData, ['assigned_hod_id' => $hod->id]));
        $response->assertSessionHasNoErrors();
        $createdStaff = User::where('email', 'engineer@example.com')->first();
        $this->assertNotNull($createdStaff);
        $this->assertEquals($hod->id, $createdStaff->assigned_hod_id);

        // 3. Excluded roles (e.g. HOD or COO) without assigned_hod_id should succeed and set assigned_hod_id to null
        $hodData = array_merge($baseData, [
            'email' => 'newhod@example.com',
            'role' => 'hod',
            'assigned_hod_id' => '',
        ]);
        $response = $this->actingAs($admin)->post('/doAddUser', $hodData);
        $response->assertSessionHasNoErrors();
        $createdHod = User::where('email', 'newhod@example.com')->first();
        $this->assertNotNull($createdHod);
        $this->assertNull($createdHod->assigned_hod_id);
    }

    public function test_hod_sees_only_assigned_staff(): void
    {
        $hodA = User::factory()->create(['role' => 4, 'email' => 'hoda@example.com', 'name' => 'HOD A']);
        $hodB = User::factory()->create(['role' => 4, 'email' => 'hodb@example.com', 'name' => 'HOD B']);

        $staffA = User::factory()->create([
            'role' => 3,
            'email' => 'staffa@example.com',
            'name' => 'Staff Member A',
            'assigned_hod_id' => $hodA->id,
        ]);

        $staffB = User::factory()->create([
            'role' => 3,
            'email' => 'staffb@example.com',
            'name' => 'Staff Member B',
            'assigned_hod_id' => $hodB->id,
        ]);

        // Test scopeForHod directly
        $hodAStaff = User::nonSuperAdmin()->forHod($hodA)->get();
        $this->assertTrue($hodAStaff->contains($staffA));
        $this->assertFalse($hodAStaff->contains($staffB));

        // Test UserController index
        $response = $this->actingAs($hodA)->get('/admin/users');
        $response->assertOk();
        $response->assertSee('Staff Member A');
        $response->assertDontSee('Staff Member B');
        $response->assertDontSee('hoda@example.com');
    }

    public function test_hod_plus_hr_can_see_all_staff_details(): void
    {
        $hodHR = User::factory()->create(['role' => 4, 'is_hr' => true, 'email' => 'hodhr@example.com', 'name' => 'HOD HR']);
        $hodB = User::factory()->create(['role' => 4, 'email' => 'hodb2@example.com', 'name' => 'HOD B2']);

        $staffA = User::factory()->create([
            'role' => 3,
            'email' => 'staffa2@example.com',
            'name' => 'Staff Member A2',
            'assigned_hod_id' => $hodHR->id,
        ]);

        $staffB = User::factory()->create([
            'role' => 3,
            'email' => 'staffb2@example.com',
            'name' => 'Staff Member B2',
            'assigned_hod_id' => $hodB->id,
        ]);

        // Test scopeForHod directly for HOD + HR
        $allStaff = User::nonSuperAdmin()->forHod($hodHR)->get();
        $this->assertTrue($allStaff->contains($staffA));
        $this->assertTrue($allStaff->contains($staffB));

        // Test UserController index for HOD + HR
        $response = $this->actingAs($hodHR)->get('/admin/users');
        $response->assertOk();
        $response->assertSee('Staff Member A2');
        $response->assertSee('Staff Member B2');
    }

    public function test_mobile_must_be_10_digits_and_pin_code_must_be_6_digits(): void
    {
        $superAdmin = User::factory()->create(['role' => 1]);
        $hod = User::factory()->create(['role' => 4]);

        $invalidData = [
            'name'            => 'Test Validation User',
            'email'           => 'invalidmobile@example.com',
            'mobile'          => '12345', // Only 5 digits
            'father_name'     => 'Father Name',
            'mother_name'     => 'Mother Name',
            'date_of_birth'   => '1995-05-15',
            'date_of_joining' => '2023-01-10',
            'gender'          => 'Male',
            'marital_status'  => 'Single',
            'house_name'      => 'House 1',
            'place'           => 'Place A',
            'po'              => 'PO Box',
            'district'        => 'District A',
            'state'           => 'State B',
            'pin_code'        => '1234', // Only 4 digits
            'aadhar_number'   => '1234 5678 9012',
            'pan_card_number' => 'ABCDE1234F',
            'account_number'  => '1234567890',
            'bank_name'       => 'Bank Name',
            'bank_branch'     => 'Branch',
            'ifsc_code'       => 'BANK0001234',
            'designation'     => 'Engineer',
            'role'            => 'engineer',
            'password'        => 'Password123!',
            'assigned_hod_id' => $hod->id,
        ];

        $response = $this->actingAs($superAdmin)->post('/doAddUser', $invalidData);
        $response->assertSessionHasErrors(['mobile', 'pin_code']);

        $validData = array_merge($invalidData, [
            'email'    => 'validmobilepin@example.com',
            'mobile'   => '9876543210', // 10 digits
            'pin_code' => '680001',     // 6 digits
        ]);

        $validResponse = $this->actingAs($superAdmin)->post('/doAddUser', $validData);
        $validResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'validmobilepin@example.com', 'mobile' => '9876543210', 'pin_code' => '680001']);
    }
}
