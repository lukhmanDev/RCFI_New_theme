<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OthersRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_others_role_cannot_add_applications(): void
    {
        $othersUser = User::factory()->create([
            'role' => 'others',
        ]);

        $response = $this->actingAs($othersUser)->postJson(route('applications.store'), [
            'applicant_name' => 'John Doe',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error' => 'Users with role "Others" cannot add applications.',
        ]);
    }

    public function test_super_admin_can_add_applications(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($superAdmin)->post(route('applications.store'), [
            'applicant_name' => 'Jane Doe',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        $response->assertStatus(302); // Redirects back or to category page on success
    }
}
