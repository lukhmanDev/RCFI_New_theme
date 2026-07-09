<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\HouseApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationApprovalPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_coo_can_approve_applications(): void
    {
        // 1. Create different users
        $superAdmin = User::create([
            'name' => 'Super Admin Test',
            'email' => 'admin_test@rcfi.org',
            'mobile' => '9999999999',
            'role' => 1,
            'password' => bcrypt('password'),
            'designation' => 'Super Admin',
        ]);

        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_test@rcfi.org',
            'mobile' => '9888888888',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $hod = User::create([
            'name' => 'HOD Test',
            'email' => 'hod_test@rcfi.org',
            'mobile' => '9777777777',
            'role' => 4,
            'password' => bcrypt('password'),
            'designation' => 'HOD',
        ]);

        // 2. Create a pending house application
        $application = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Test Applicant',
            'amount_requested' => 100000,
            'status' => 'Pending',
            'place' => 'Test Place',
        ]);

        // 3. Super Admin attempts to approve -> Should fail / redirect back with error
        $response = $this->actingAs($superAdmin)->post("/admin/applications/house/{$application->id}/approve");
        $response->assertSessionHas('error', 'You are not authorized to approve applications.');
        $this->assertEquals('Pending', $application->fresh()->status);

        // 4. HOD attempts to approve -> Should fail / redirect back with error
        $response = $this->actingAs($hod)->post("/admin/applications/house/{$application->id}/approve");
        $response->assertSessionHas('error', 'You are not authorized to approve applications.');
        $this->assertEquals('Pending', $application->fresh()->status);

        // 5. COO attempts to approve -> Should succeed
        $response = $this->actingAs($coo)->post("/admin/applications/house/{$application->id}/approve");
        $this->assertEquals('Approved', $application->fresh()->status);
    }

    public function test_only_coo_can_reject_applications(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin Test',
            'email' => 'admin_test2@rcfi.org',
            'mobile' => '9999999999',
            'role' => 1,
            'password' => bcrypt('password'),
            'designation' => 'Super Admin',
        ]);

        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_test2@rcfi.org',
            'mobile' => '9888888888',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $application = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Test Applicant',
            'amount_requested' => 100000,
            'status' => 'Pending',
            'place' => 'Test Place',
        ]);

        // Super Admin attempts to reject -> Should fail
        $response = $this->actingAs($superAdmin)->post("/admin/applications/house/{$application->id}/reject");
        $response->assertSessionHas('error', 'You are not authorized to reject applications.');
        $this->assertEquals('Pending', $application->fresh()->status);

        // COO attempts to reject -> Should succeed
        $response = $this->actingAs($coo)->post("/admin/applications/house/{$application->id}/reject");
        $this->assertEquals('Rejected', $application->fresh()->status);
    }

    public function test_applications_dashboard_shows_pending_counts(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_test3@rcfi.org',
            'mobile' => '9888888887',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        // Create one pending house application
        HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Pending Applicant',
            'amount_requested' => 100000,
            'status' => 'Pending',
            'place' => 'Test Place',
        ]);

        // Go to dashboard
        $response = $this->actingAs($coo)->get('/admin/applications');
        $response->assertStatus(200);
        $response->assertSee('Pending');
    }

    public function test_approved_applications_page_displays_approved_records(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_test4@rcfi.org',
            'mobile' => '9888888886',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        // Create one approved house application
        $app = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Approved Applicant Name ABC',
            'amount_requested' => 150000,
            'status' => 'Approved',
            'place' => 'Test Approved Place',
        ]);

        // Create corresponding house project
        \App\Models\HouseProject::create([
            'application_id' => $app->id,
            'project_id' => 'PRJ-HOUSE-9999',
            'type_of_project' => 'House',
            'status' => 'Ongoing',
            'available_budget' => 150000,
        ]);

        // Go to approved applications list
        $response = $this->actingAs($coo)->get('/admin/applications/approved/category/house');
        $response->assertStatus(200);
        $response->assertSee('Approved Applicant Name ABC');
        $response->assertSee('Test Approved Place');
        $response->assertSee('RCFI/' . date('y') . '-HS');
        // Status column now shows project_phase (not set = 'Not set'), not the old project->status field
        $response->assertSee('Not set');
    }

    public function test_approved_applications_dashboard(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_test5@rcfi.org',
            'mobile' => '9888888885',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $response = $this->actingAs($coo)->get('/admin/applications/approved');
        $response->assertStatus(200);
        $response->assertSee('Approved Applications Dashboard');
    }

    public function test_only_approved_applications_can_be_assigned_to_projects(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_test6@rcfi.org',
            'mobile' => '9888888884',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $project = \App\Models\HouseProject::create([
            'project_id' => 'PRJ-HOUSE-8888',
            'type_of_project' => 'House',
            'status' => 'Pending',
            'available_budget' => 150000,
        ]);

        // Create one pending application
        $pendingApp = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Pending Applicant X',
            'status' => 'Pending',
        ]);

        // Create one approved application
        $approvedApp = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Approved Applicant Y',
            'status' => 'Approved',
        ]);

        // Attempt to assign pending application -> Should fail with error message
        $response = $this->actingAs($coo)->post("/admin/projects/{$project->id}/assign-application", [
            'application_id' => $pendingApp->id
        ]);
        $response->assertSessionHas('error', 'Only approved applications can be assigned.');
        $this->assertNull($project->fresh()->application_id);

        // Attempt to assign approved application -> Should succeed
        $response = $this->actingAs($coo)->post("/admin/projects/{$project->id}/assign-application", [
            'application_id' => $approvedApp->id
        ]);
        $this->assertEquals($approvedApp->id, $project->fresh()->application_id);
    }

    public function test_stage_1_2_3_do_not_need_coo_approval(): void
    {
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff_user@rcfi.org',
            'mobile' => '9888888883',
            'role' => 3,
            'password' => bcrypt('password'),
            'designation' => 'Project Manager',
        ]);

        $project = \App\Models\EducationCenterProject::create([
            'project_id' => 'PRJ-EC-7777',
            'type_of_project' => 'Education Center',
            'status' => 'Pending',
            'stage' => 4,
            'available_budget' => 500000,
        ]);

        $response = $this->actingAs($staff)->post("/admin/projects/{$project->id}/approve");
        $response->assertSessionHas('error', 'Only COO is authorized to approve Stage 4.');
        $this->assertEquals(4, $project->fresh()->stage);
    }

    public function test_project_manager_can_toggle_file_checklist(): void
    {
        $pm = User::create([
            'name' => 'PM User',
            'email' => 'pm_user@rcfi.org',
            'mobile' => '9888888899',
            'role' => 3,
            'password' => bcrypt('password'),
            'designation' => 'Project Manager',
        ]);

        $project = \App\Models\HouseProject::create([
            'project_id' => 'PRJ-HS-1111',
            'type_of_project' => 'House',
            'status' => 'Pending',
            'stage' => 3,
            'available_budget' => 200000,
        ]);

        // Attempt to toggle "Land document" -> Should succeed
        $response = $this->actingAs($pm)->post("/admin/projects/{$project->id}/toggle-file", [
            'document_name' => 'Land document'
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals("1", $project->fresh()->files['Land document'] ?? null);

        // Toggle again -> Should untick (unset)
        $response = $this->actingAs($pm)->post("/admin/projects/{$project->id}/toggle-file", [
            'document_name' => 'Land document'
        ]);

        $response->assertSessionHas('success');
        $this->assertArrayNotHasKey('Land document', $project->fresh()->files ?? []);
    }
}
