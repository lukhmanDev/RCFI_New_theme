<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use Database\Seeders\LeaveTypeSeeder;

class CooStaffDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LeaveTypeSeeder::class);
    }

    public function test_coo_and_super_admin_can_access_staff_dashboard(): void
    {
        $coo = User::factory()->create(['role' => 'coo']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $responseCoo = $this->actingAs($coo)->get('/admin/coo/staff-dashboard');
        $responseCoo->assertStatus(200);
        $responseCoo->assertSeeLivewire('coo-staff-dashboard');

        $responseAdmin = $this->actingAs($superAdmin)->get('/admin/coo/staff-dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSeeLivewire('coo-staff-dashboard');
    }

    public function test_unauthorized_staff_cannot_access_coo_staff_dashboard(): void
    {
        $staff = User::factory()->create(['role' => 'reception']);

        $response = $this->actingAs($staff)->get('/admin/coo/staff-dashboard');
        $response->assertStatus(403);
    }

    public function test_coo_can_approve_staff_leave_request(): void
    {
        $coo = User::factory()->create(['role' => 'coo']);
        $staff = User::factory()->create(['role' => 'engineer']);
        $clType = LeaveType::where('leave_code', 'CL')->first();

        $req = LeaveRequest::create([
            'user_id' => $staff->id,
            'leave_type_id' => $clType->id,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'total_days' => 2,
            'reason' => 'Family occasion',
            'status' => 'Pending',
            'applied_on' => now(),
        ]);

        $this->actingAs($coo);

        \Livewire\Livewire::test(\App\Livewire\CooStaffDashboard::class)
            ->call('approveLeave', $req->id);

        $this->assertEquals('Approved', $req->fresh()->status);
    }

    public function test_coo_can_edit_staff_details_from_dashboard(): void
    {
        $coo = User::factory()->create(['role' => 'coo']);
        $staff = User::factory()->create([
            'role' => 'engineer',
            'name' => 'Original Staff Name',
            'designation' => 'Junior Engineer',
        ]);

        $this->actingAs($coo);

        \Livewire\Livewire::test(\App\Livewire\CooStaffDashboard::class)
            ->call('openEditStaffModal', $staff->id)
            ->set('editName', 'Updated Staff Name')
            ->set('editEmail', 'updatedstaff@example.com')
            ->set('editMobile', '9876543210')
            ->set('editDesignation', 'Senior Lead Engineer')
            ->set('editRole', 'project_manager')
            ->set('editHouseName', 'Rose Villa')
            ->set('editPlace', 'Kochi')
            ->call('saveStaffDetails')
            ->assertHasNoErrors();

        $updatedStaff = $staff->fresh();
        $this->assertEquals('Updated Staff Name', $updatedStaff->name);
        $this->assertEquals('Senior Lead Engineer', $updatedStaff->designation);
        $this->assertEquals('project_manager', $updatedStaff->role);
        $this->assertEquals('Rose Villa', $updatedStaff->house_name);
        $this->assertEquals('Kochi', $updatedStaff->place);
    }

    public function test_super_admin_is_hidden_from_staff_dashboard(): void
    {
        $coo = User::factory()->create(['role' => 'coo']);
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin Secret',
            'role' => 'super_admin',
            'email' => 'sdigibeat@gmail.com',
        ]);
        $regularStaff = User::factory()->create(['role' => 'engineer', 'name' => 'Regular Staff Member']);

        $this->actingAs($coo);

        \Livewire\Livewire::test(\App\Livewire\CooStaffDashboard::class)
            ->assertDontSee('Super Admin Secret')
            ->assertSee('Regular Staff Member');
    }

    public function test_coo_can_toggle_between_grid_and_table_view_modes(): void
    {
        $coo = User::factory()->create(['role' => 'coo']);
        User::factory()->create(['role' => 'engineer', 'name' => 'Card Staff User']);

        $this->actingAs($coo);

        \Livewire\Livewire::test(\App\Livewire\CooStaffDashboard::class)
            ->assertSet('viewMode', 'grid')
            ->assertSee('Card Staff User')
            ->call('setViewMode', 'table')
            ->assertSet('viewMode', 'table')
            ->assertSee('Card Staff User');
    }

    public function test_super_admin_can_add_staff_from_coo_dashboard(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $hod = User::factory()->create(['role' => 'hod']);

        $this->actingAs($superAdmin);

        \Livewire\Livewire::test(\App\Livewire\CooStaffDashboard::class)
            ->call('openAddStaffModal')
            ->assertSet('showAddStaffModal', true)
            ->set('addName', 'New Livewire Staff')
            ->set('addEmail', 'newlivewirestaff@example.com')
            ->set('addMobile', '9876543210')
            ->set('addFatherName', 'Father Name')
            ->set('addMotherName', 'Mother Name')
            ->set('addDateOfBirth', '1990-01-01')
            ->set('addDateOfJoining', '2020-06-01')
            ->set('addGender', 'Male')
            ->set('addMaritalStatus', 'Single')
            ->set('addHouseName', 'Rose House')
            ->set('addPlace', 'Kochi')
            ->set('addPo', 'Kochi PO')
            ->set('addDistrict', 'Ernakulam')
            ->set('addState', 'Kerala')
            ->set('addPinCode', '682001')
            ->set('addAadharNumber', '123456789012')
            ->set('addPanCardNumber', 'ABCDE1234F')
            ->set('addAccountNumber', '987654321012')
            ->set('addBankName', 'State Bank of India')
            ->set('addBankBranch', 'Kochi Main')
            ->set('addIfscCode', 'SBIN0001234')
            ->set('addDesignation', 'Lead Engineer')
            ->set('addRole', 'engineer')
            ->set('addHodId', $hod->id)
            ->set('addPassword', 'password123')
            ->call('createStaff')
            ->assertHasNoErrors()
            ->assertSet('showAddStaffModal', false);

        $this->assertDatabaseHas('users', [
            'email' => 'newlivewirestaff@example.com',
            'name' => 'New Livewire Staff',
            'designation' => 'Lead Engineer',
            'house_name' => 'Rose House',
        ]);
    }
}
