<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use App\Jobs\AccrueCasualLeaveJob;
use App\Jobs\AllocateAnnualLeaveJob;
use Database\Seeders\LeaveTypeSeeder;
use Carbon\Carbon;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LeaveTypeSeeder::class);
    }

    public function test_eligibility_logic_for_all_seven_leave_types(): void
    {
        $cl = LeaveType::where('leave_code', 'CL')->first();
        $sl = LeaveType::where('leave_code', 'SL')->first();
        $lsl = LeaveType::where('leave_code', 'LSL')->first();
        $ml = LeaveType::where('leave_code', 'ML')->first();
        $mtl = LeaveType::where('leave_code', 'MTL')->first();
        $ptl = LeaveType::where('leave_code', 'PTL')->first();
        $pil = LeaveType::where('leave_code', 'PIL')->first();

        // 1. Male Single User (new joiner)
        $maleSingleNew = User::factory()->create([
            'gender' => 'Male',
            'marital_status' => 'Single',
            'date_of_joining' => now()->subMonths(6),
        ]);

        $this->assertTrue($maleSingleNew->isEligibleFor($cl));
        $this->assertTrue($maleSingleNew->isEligibleFor($sl));
        $this->assertFalse($maleSingleNew->isEligibleFor($lsl)); // < 5 years
        $this->assertTrue($maleSingleNew->isEligibleFor($ml));
        $this->assertFalse($maleSingleNew->isEligibleFor($mtl)); // Female only
        $this->assertFalse($maleSingleNew->isEligibleFor($ptl)); // Married only
        $this->assertTrue($maleSingleNew->isEligibleFor($pil));

        // 2. Female Married User (6 years service)
        $femaleMarriedSenior = User::factory()->create([
            'gender' => 'Female',
            'marital_status' => 'Married',
            'date_of_joining' => now()->subYears(6),
        ]);

        $this->assertTrue($femaleMarriedSenior->isEligibleFor($cl));
        $this->assertTrue($femaleMarriedSenior->isEligibleFor($sl));
        $this->assertTrue($femaleMarriedSenior->isEligibleFor($lsl)); // >= 5 years
        $this->assertTrue($femaleMarriedSenior->isEligibleFor($mtl)); // Married female
        $this->assertFalse($femaleMarriedSenior->isEligibleFor($ptl)); // Male only
    }

    public function test_balance_deduction_on_approval(): void
    {
        $user = User::factory()->create(['role' => 'engineer']);
        $approver = User::factory()->create(['role' => 'super_admin']);
        $type = LeaveType::where('leave_code', 'SL')->first();

        $service = app(LeaveService::class);
        $req = $service->submitRequest($user, $type, now()->addDays(1)->format('Y-m-d'), now()->addDays(2)->format('Y-m-d'), 'Feeling sick');

        $this->assertEquals('Pending', $req->status);

        $service->approve($req, $approver);

        $this->assertEquals('Approved', $req->fresh()->status);
        $balance = LeaveBalance::where('user_id', $user->id)->where('leave_type_id', $type->id)->first();
        $this->assertNotNull($balance);
        $this->assertGreaterThan(0, $balance->used_days);
    }

    public function test_accrual_job_idempotency(): void
    {
        $user = User::factory()->create();

        // Dispatch Casual Leave accrual job twice
        AccrueCasualLeaveJob::dispatchSync();
        AccrueCasualLeaveJob::dispatchSync();

        $clType = LeaveType::where('leave_code', 'CL')->first();
        $balance = LeaveBalance::where('user_id', $user->id)->where('leave_type_id', $clType->id)->first();

        $this->assertNotNull($balance);
        // Balance allocated_days should be exactly 1.0, not 2.0 (idempotent check)
        $this->assertEquals(1.0, $balance->allocated_days);
    }

    public function test_unauthorized_users_cannot_approve_or_reject(): void
    {
        $staff = User::factory()->create(['role' => 'engineer']);
        $otherStaff = User::factory()->create(['role' => 'reception']);
        $type = LeaveType::where('leave_code', 'SL')->first();

        $service = app(LeaveService::class);
        $req = $service->submitRequest($staff, $type, now()->addDays(1)->format('Y-m-d'), now()->addDays(2)->format('Y-m-d'), 'Test');

        $this->actingAs($otherStaff);

        $response = $this->patchJson("/api/leave-requests/{$req->id}/approve");
        $response->assertStatus(403);
    }

    public function test_user_can_submit_leave_request_via_web_route(): void
    {
        $user = User::factory()->create(['role' => 'engineer']);
        $type = LeaveType::where('leave_code', 'CL')->first();

        $response = $this->actingAs($user)->post('/leave-request', [
            'leave_type_id' => $type->id,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'reason' => 'Family event',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $user->id,
            'leave_type_id' => $type->id,
            'reason' => 'Family event',
            'status' => 'Pending',
        ]);
    }
}
