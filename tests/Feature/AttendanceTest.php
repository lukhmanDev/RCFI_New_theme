<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_clock_in_and_clock_out(): void
    {
        $user = User::factory()->create(['role' => 'engineer']);
        $service = app(AttendanceService::class);

        // Clock In
        $attendance = $service->clockIn($user, 'Starting morning shift');
        $this->assertNotNull($attendance);
        $this->assertEquals($user->id, $attendance->user_id);
        $this->assertNotNull($attendance->clock_in);
        $this->assertEquals('Starting morning shift', $attendance->notes);

        // Clock Out
        $clockedOut = $service->clockOut($user, 'Finished day work');
        $this->assertNotNull($clockedOut->clock_out);
        $this->assertStringContainsString('Finished day work', $clockedOut->notes);
    }

    public function test_admin_can_mark_attendance_for_staff(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $staff = User::factory()->create(['role' => 'engineer']);
        $service = app(AttendanceService::class);

        $date = now()->format('Y-m-d');
        $attendance = $service->markAttendance(
            $staff,
            $date,
            'Present',
            '09:00:00',
            '17:30:00',
            'Manually marked by Admin',
            $admin
        );

        $this->assertEquals('Present', $attendance->status);
        $this->assertEquals('09:00:00', $attendance->clock_in);
        $this->assertEquals('17:30:00', $attendance->clock_out);
        $this->assertEquals($admin->id, $attendance->marked_by);
    }

    public function test_working_hours_calculation(): void
    {
        $staff = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'date' => now()->format('Y-m-d'),
            'clock_in' => '09:00:00',
            'clock_out' => '17:30:00',
            'status' => 'Present',
        ]);

        $this->assertEquals('8h 30m', $attendance->working_hours);
    }

    public function test_user_can_access_attendance_portal_route(): void
    {
        $user = User::factory()->create(['role' => 'engineer']);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSeeLivewire('attendance-widget');
    }

    public function test_admin_can_access_attendance_admin_route(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->get('/attendance/admin');
        $response->assertStatus(200);
        $response->assertSeeLivewire('attendance-admin-dashboard');
    }
}
