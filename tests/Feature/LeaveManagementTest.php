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

    public function test_is_hr_single_flag_auto_flip_constraint(): void
    {
        $hod1 = User::factory()->create(['role' => 'hod', 'is_hr' => true]);
        $this->assertTrue((bool)$hod1->fresh()->is_hr);

        $hod2 = User::factory()->create(['role' => 'hod', 'is_hr' => true]);
        $this->assertTrue((bool)$hod2->fresh()->is_hr);
        $this->assertFalse((bool)$hod1->fresh()->is_hr); // Auto-flipped to false
    }

    public function test_hod_approval_scope_and_hr_hod_all_access(): void
    {
        $hod1 = User::factory()->create(['role' => 'hod', 'is_hr' => false]);
        $hrHod = User::factory()->create(['role' => 'hod', 'is_hr' => true]);

        $staff1 = User::factory()->create(['role' => 'engineer', 'assigned_hod_id' => $hod1->id]);
        $staff2 = User::factory()->create(['role' => 'reception', 'assigned_hod_id' => $hrHod->id]);

        $type = LeaveType::where('leave_code', 'CL')->first();
        $service = app(LeaveService::class);

        $req1 = $service->submitRequest($staff1, $type, now()->addDays(1)->format('Y-m-d'), now()->addDays(2)->format('Y-m-d'), 'Req 1');
        $req2 = $service->submitRequest($staff2, $type, now()->addDays(3)->format('Y-m-d'), now()->addDays(4)->format('Y-m-d'), 'Req 2');

        // Regular HOD1 can approve assigned staff1, but NOT staff2
        $this->assertTrue($hod1->can('approve', $req1));
        $this->assertFalse($hod1->can('approve', $req2));

        // HR HOD can approve ALL staff requests
        $this->assertTrue($hrHod->can('approve', $req1));
        $this->assertTrue($hrHod->can('approve', $req2));
    }

    public function test_organization_hierarchy_tree_structure(): void
    {
        $coo = User::factory()->create(['role' => 'coo', 'name' => 'COO Executive']);
        $hod1 = User::factory()->create(['role' => 'hod', 'name' => 'Engineering HOD', 'is_hr' => false]);
        $hrHod = User::factory()->create(['role' => 'hod', 'name' => 'HR HOD', 'is_hr' => true]);

        $staffA = User::factory()->create(['role' => 'engineer', 'name' => 'Engineer A', 'assigned_hod_id' => $hod1->id]);
        $staffB = User::factory()->create(['role' => 'reception', 'name' => 'Reception B', 'assigned_hod_id' => $hrHod->id]);

        $tree = User::getHierarchyTree();

        $this->assertEquals($coo->id, $tree['id']);
        $this->assertCount(2, $tree['children']);

        $hod1Node = collect($tree['children'])->firstWhere('id', $hod1->id);
        $this->assertNotNull($hod1Node);
        $this->assertCount(1, $hod1Node['children']);
        $this->assertEquals('Engineer A', $hod1Node['children'][0]['name']);
    }

    public function test_leave_approval_audit_logging(): void
    {
        $hod = User::factory()->create(['role' => 'hod', 'is_hr' => false]);
        $staff = User::factory()->create(['role' => 'engineer', 'assigned_hod_id' => $hod->id]);
        $type = LeaveType::where('leave_code', 'SL')->first();

        $service = app(LeaveService::class);
        $req = $service->submitRequest($staff, $type, now()->addDays(1)->format('Y-m-d'), now()->addDays(2)->format('Y-m-d'), 'Sick leave');

        $service->approve($req, $hod);

        $this->assertDatabaseHas('leave_approval_logs', [
            'leave_request_id' => $req->id,
            'approver_id' => $hod->id,
            'action' => 'Approved',
            'is_backup_approver' => false,
        ]);
    }

    public function test_leave_without_pay_unlimited_submission_and_approval(): void
    {
        $staff = User::factory()->create(['role' => 'engineer']);
        $hod = User::factory()->create(['role' => 'hod']);
        $lwp = LeaveType::where('leave_code', 'LWP')->first();
        $this->assertNotNull($lwp);

        $service = app(LeaveService::class);
        $req = $service->submitRequest($staff, $lwp, now()->addDays(1)->format('Y-m-d'), now()->addDays(10)->format('Y-m-d'), 'Taking leave without pay');

        $this->assertGreaterThan(0, $req->total_days);
        $this->assertEquals('Pending', $req->status);

        $service->approve($req, $hod);

        $this->assertEquals('Approved', $req->fresh()->status);
        $balance = LeaveBalance::where('user_id', $staff->id)->where('leave_type_id', $lwp->id)->first();
        $this->assertNotNull($balance);
        $this->assertEquals($req->total_days, $balance->used_days);
    }

    public function test_approved_leave_application_cannot_be_deleted(): void
    {
        $staff = User::factory()->create(['role' => 'engineer']);
        $hod = User::factory()->create(['role' => 'hod']);
        $type = LeaveType::where('leave_code', 'CL')->first();

        $service = app(LeaveService::class);
        $req = $service->submitRequest($staff, $type, now()->addDays(1)->format('Y-m-d'), now()->addDays(2)->format('Y-m-d'), 'Test Leave');
        $service->approve($req, $hod);

        $this->actingAs($hod);
        $response = $this->delete(route('leave.destroy', $req->id));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('leave_requests', ['id' => $req->id, 'status' => 'Approved']);
    }

    public function test_half_day_leave_submission_and_approval(): void
    {
        $staff = User::factory()->create(['role' => 'engineer']);
        $hod = User::factory()->create(['role' => 'hod']);
        $cl = LeaveType::where('leave_code', 'CL')->first();

        $service = app(LeaveService::class);
        $date = now()->addDays(5)->format('Y-m-d');
        $req = $service->submitRequest($staff, $cl, $date, $date, 'Half day doctor appointment', true, 'First Half');

        $this->assertEquals(0.5, $req->total_days);
        $this->assertTrue($req->is_half_day);
        $this->assertEquals('First Half', $req->half_day_session);
        $this->assertEquals('Pending', $req->status);

        $service->approve($req, $hod);

        $this->assertEquals('Approved', $req->fresh()->status);
        $balance = LeaveBalance::where('user_id', $staff->id)->where('leave_type_id', $cl->id)->first();
        $this->assertNotNull($balance);
        $this->assertEquals(0.5, $balance->used_days);
    }

    public function test_other_leave_restricted_to_hr_coo_super_admin(): void
    {
        $ol = LeaveType::where('leave_code', 'OL')->first();
        $this->assertNotNull($ol);

        $staff = User::factory()->create(['role' => 'engineer', 'is_hr' => false]);
        $hrHod = User::factory()->create(['role' => 'hod', 'is_hr' => true]);
        $coo = User::factory()->create(['role' => 'coo']);

        // Standard staff is not eligible for Other Leave
        $this->assertFalse($staff->isEligibleFor($ol));

        // HR HOD and COO are eligible
        $this->actingAs($hrHod);
        $this->assertTrue($hrHod->isEligibleFor($ol));

        $this->actingAs($coo);
        $this->assertTrue($coo->isEligibleFor($ol));

        // HR HOD can apply Other Leave for a staff member profile
        $this->actingAs($hrHod);
        $response = $this->post(route('leave.request'), [
            'user_id' => $staff->id,
            'leave_type_id' => $ol->id,
            'from_date' => now()->addDays(2)->format('Y-m-d'),
            'to_date' => now()->addDays(3)->format('Y-m-d'),
            'reason' => 'Discretionary Other Leave assigned by HR',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $staff->id,
            'leave_type_id' => $ol->id,
            'reason' => 'Discretionary Other Leave assigned by HR',
        ]);
    }
}
