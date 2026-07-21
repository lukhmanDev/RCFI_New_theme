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
        $staff = User::factory()->create(['role' => 3, 'name' => 'Original Name']);

        $response = $this->actingAs($coo)->put("/admin/users/{$staff->id}", [
            'name' => 'Updated Name',
            'email' => $staff->email,
            'role' => 4,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Name', $staff->fresh()->name);
        $this->assertEquals('project_manager', $staff->fresh()->role);
    }

    public function test_coo_creates_user_with_default_others_role(): void
    {
        $coo = User::factory()->create(['role' => 2]);

        $response = $this->actingAs($coo)->post('/doAddUser', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'password123',
            'role' => 2,
        ]);

        $response->assertRedirect();
        $newUser = User::where('email', 'newstaff@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('others', $newUser->role);
    }

    public function test_coo_cannot_change_user_designation_during_update(): void
    {
        $coo = User::factory()->create(['role' => 2]);
        $staff = User::factory()->create(['role' => 3, 'designation' => 'Original Designation']);

        $response = $this->actingAs($coo)->put("/admin/users/{$staff->id}", [
            'name' => $staff->name,
            'email' => $staff->email,
            'designation' => 'Updated Designation',
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
}
