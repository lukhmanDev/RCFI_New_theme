<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/profile');

        $response->assertStatus(200);
        $response->assertSee('Profile Details');
        $response->assertSee('Security');
    }

    public function test_user_can_update_profile_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'designation' => 'Old Designation',
            'mobile' => '123456',
        ]);

        $response = $this->actingAs($user)->post('/admin/profile', [
            'name' => 'New Name',
            'designation' => 'New Designation',
            'mobile' => '987654321',
            'address' => '123 New Street, New City',
        ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $user->fresh()->name);
        $this->assertEquals('New Designation', $user->fresh()->designation);
        $this->assertEquals('987654321', $user->fresh()->mobile);
        $this->assertEquals('123 New Street, New City', $user->fresh()->profile->address);
    }

    public function test_non_super_admin_cannot_update_own_designation(): void
    {
        $user = User::factory()->create([
            'role' => 3, // Project Manager
            'designation' => 'Old Designation',
        ]);

        $response = $this->actingAs($user)->post('/admin/profile', [
            'name' => 'New Name',
            'designation' => 'New Designation',
            'mobile' => '987654321',
        ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $user->fresh()->name);
        $this->assertEquals('Old Designation', $user->fresh()->designation);
    }

    public function test_unverified_user_cannot_update_credentials(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->post('/admin/profile/credentials', [
            'email' => 'new@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $this->assertEquals('old@example.com', $user->fresh()->email);
    }

    public function test_email_verification_flow_and_credentials_update(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'email' => 'old@example.com',
        ]);

        // 1. Send Code
        $response = $this->actingAs($user)->post('/admin/profile/send-code');
        $response->assertRedirect();

        Mail::assertSent(function (VerificationCodeMail $mail) use ($user) {
            return $mail->to[0]['address'] === $user->email;
        });

        $code = session('email_verification_code');
        $this->assertNotEmpty($code);

        // 2. Verify with wrong code
        $response = $this->actingAs($user)
                         ->withSession(['email_verification_code' => (string)$code])
                         ->post('/admin/profile/verify', [
                             'code' => '000000',
                         ]);
        $response->assertSessionHasErrors('code');
        $this->assertNull($user->fresh()->email_verified_at);

        // 3. Verify with correct code
        $response = $this->actingAs($user)
                         ->withSession(['email_verification_code' => (string)$code])
                         ->post('/admin/profile/verify', [
                             'code' => (string)$code,
                         ]);
        $response->assertRedirect();
        $this->assertNotNull($user->fresh()->email_verified_at);

        // 4. Update credentials
        $response = $this->actingAs($user)->post('/admin/profile/credentials', [
            'email' => 'new@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $this->assertEquals('new@example.com', $user->fresh()->email);
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertNull($user->fresh()->email_verified_at);

        Mail::assertSent(function (VerificationCodeMail $mail) {
            return $mail->to[0]['address'] === 'new@example.com';
        });
    }
}
