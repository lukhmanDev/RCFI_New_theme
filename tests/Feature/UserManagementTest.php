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
}
