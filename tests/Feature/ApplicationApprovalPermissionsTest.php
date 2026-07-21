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

        // COO attempts to reject -> Should succeed with rejection reason
        $response = $this->actingAs($coo)->post("/admin/applications/house/{$application->id}/reject", [
            'remarks' => 'Incomplete documents provided.'
        ]);
        $freshApp = $application->fresh();
        $this->assertEquals('Rejected', $freshApp->status);
        $this->assertStringContainsString('Rejection Reason: Incomplete documents provided.', $freshApp->details);
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
            'project_manager_id' => $staff->id,
        ]);

        $response = $this->actingAs($staff)->post("/admin/projects/{$project->id}/approve");
        $response->assertSessionHas('error', 'Only COO or HOD is authorized to approve Stage 4.');
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

        $app = \App\Models\HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Approved Toggle Applicant',
            'status' => 'Approved',
        ]);

        $project = \App\Models\HouseProject::create([
            'project_id' => 'PRJ-HS-1111',
            'type_of_project' => 'House',
            'status' => 'Pending',
            'stage' => 3,
            'available_budget' => 200000,
            'application_id' => $app->id,
            'project_manager_id' => $pm->id,
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

    public function test_only_pending_applications_can_be_deleted(): void
    {
        $coo = User::create([
            'name' => 'COO Test App Delete',
            'email' => 'coo_test_delete@rcfi.org',
            'mobile' => '9888888812',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        // 1. Create a pending application
        $pendingApp = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Pending Applicant Delete',
            'status' => 'Pending',
        ]);

        // 2. Create an approved application
        $approvedApp = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Approved Applicant Delete',
            'status' => 'Approved',
        ]);

        // 3. Try to delete the approved application -> should fail
        $response = $this->actingAs($coo)->delete("/admin/applications/{$approvedApp->id}", [
            'redirect_category' => 'house'
        ]);
        $response->assertSessionHas('error', 'Only pending applications can be deleted.');
        $this->assertNotNull(HouseApplication::find($approvedApp->id));

        // 4. Try to delete the pending application -> should succeed
        $response = $this->actingAs($coo)->delete("/admin/applications/{$pendingApp->id}", [
            'redirect_category' => 'house'
        ]);
        $response->assertRedirect('/admin/applications/category/house');
        $response->assertSessionHas('success');
        $this->assertNull(HouseApplication::find($pendingApp->id));
    }

    public function test_orphan_care_cluster_and_agency_validation(): void
    {
        $coo = User::create([
            'name' => 'COO Test App validation',
            'email' => 'coo_test_val@rcfi.org',
            'mobile' => '9888888823',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $cluster = \App\Models\Cluster::create([
            'name' => 'Test Cluster',
            'code' => 'TC01',
        ]);

        // 1. Store a pending orphan care application without cluster and agency (should succeed)
        $response = $this->actingAs($coo)->post('/admin/applications', [
            'applicant_name' => 'Pending Orphan',
            'category' => 'Orphan Care',
            'redirect_category' => 'orphan-care',
            'status' => 'Pending',
        ]);
        $response->assertRedirect('/admin/applications/category/orphan-care');
        $response->assertSessionHas('success');

        $app = \App\Models\OrphanCareApplication::where('applicant_name', 'Pending Orphan')->first();
        $this->assertNotNull($app);
        $this->assertNull($app->cluster_id);
        $this->assertNull($app->agency_number);

        // 2. Attempt to approve without cluster_id and agency_number (should fail validation)
        $response = $this->actingAs($coo)->post("/admin/applications/orphan-care/{$app->id}/approve", []);
        $response->assertSessionHasErrors(['cluster_id', 'agency_number']);
        $this->assertEquals('Pending', $app->fresh()->status);

        // 3. Approve with cluster_id and agency_number (should succeed)
        $response = $this->actingAs($coo)->post("/admin/applications/orphan-care/{$app->id}/approve", [
            'cluster_id' => $cluster->id,
            'agency_number' => 'AG123',
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('Approved', $app->fresh()->status);
        $this->assertEquals($cluster->id, $app->fresh()->cluster_id);
        $this->assertEquals('AG123', $app->fresh()->agency_number);

        // 4. Create another pending application to test updating approved with missing fields
        $app2 = \App\Models\OrphanCareApplication::create([
            'category' => 'Orphan Care',
            'applicant_name' => 'Orphan Two',
            'status' => 'Pending',
        ]);

        // Update to approved status without cluster and agency -> should fail
        $response = $this->actingAs($coo)->put("/admin/applications/{$app2->id}", [
            'applicant_name' => 'Orphan Two Updated',
            'category' => 'Orphan Care',
            'redirect_category' => 'orphan-care',
            'status' => 'Approved',
        ]);
        $response->assertSessionHasErrors(['cluster_id', 'agency_number']);
    }

    public function test_orphan_care_sponsor_status_toggling(): void
    {
        $coo = User::create([
            'name' => 'COO Sponsor Test',
            'email' => 'coo_sponsor@rcfi.org',
            'mobile' => '9888888824',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $pm = User::create([
            'name' => 'PM Sponsor Test',
            'email' => 'pm_sponsor@rcfi.org',
            'mobile' => '9888888825',
            'role' => 5,
            'password' => bcrypt('password'),
            'designation' => 'Project Manager',
        ]);

        // 1. Create an approved orphan care application
        $app = \App\Models\OrphanCareApplication::create([
            'category' => 'Orphan Care',
            'applicant_name' => 'Orphan Sponsor Test',
            'status' => 'Approved',
        ]);

        // Assert default sponsor status is 'Not Sponsored'
        $this->assertEquals('Not Sponsored', $app->sponsor_status);

        // 2. PM attempts to toggle -> Should fail / redirect with error
        $response = $this->actingAs($pm)->post("/admin/applications/orphan-care/{$app->id}/toggle-sponsor");
        $response->assertSessionHas('error', 'You are not authorized to update sponsor status.');
        $this->assertEquals('Not Sponsored', $app->fresh()->sponsor_status);

        // 3. COO attempts to toggle -> Should succeed (toggle to Sponsored)
        $response = $this->actingAs($coo)->post("/admin/applications/orphan-care/{$app->id}/toggle-sponsor");
        $response->assertSessionHas('success', 'Sponsor status updated successfully.');
        $this->assertEquals('Sponsored', $app->fresh()->sponsor_status);

        // Assert OrphanCareProject was automatically created
        $project = \App\Models\OrphanCareProject::where('application_id', $app->id)->first();
        $this->assertNotNull($project);
        $this->assertEquals($app->applicant_name, $project->project_name);

        // 4. COO toggles again -> Should toggle back to 'Not Sponsored'
        $response = $this->actingAs($coo)->post("/admin/applications/orphan-care/{$app->id}/toggle-sponsor");
        $response->assertSessionHas('success', 'Sponsor status updated successfully.');
        $this->assertEquals('Not Sponsored', $app->fresh()->sponsor_status);

        // Assert OrphanCareProject was automatically deleted
        $this->assertNull(\App\Models\OrphanCareProject::where('application_id', $app->id)->first());
    }

    public function test_orphan_care_project_assign_only_sponsored(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_assign_sponsor@rcfi.org',
            'mobile' => '9888888877',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $project = \App\Models\OrphanCareProject::create([
            'project_id' => 'PRJ-OC-9999',
            'type_of_project' => 'Orphan Care',
            'status' => 'Pending',
        ]);

        // Create one approved, not sponsored application
        $notSponsoredApp = \App\Models\OrphanCareApplication::create([
            'category' => 'Orphan Care',
            'applicant_name' => 'Not Sponsored Student',
            'status' => 'Approved',
            'sponsor_status' => 'Not Sponsored',
        ]);

        // Create one approved, sponsored application
        $sponsoredApp = \App\Models\OrphanCareApplication::create([
            'category' => 'Orphan Care',
            'applicant_name' => 'Sponsored Student',
            'status' => 'Approved',
            'sponsor_status' => 'Sponsored',
        ]);

        // Attempt to assign not sponsored application -> Should fail
        $response = $this->actingAs($coo)->post("/admin/projects/{$project->id}/assign-application", [
            'application_id' => $notSponsoredApp->id
        ]);
        $response->assertSessionHas('error', 'Only sponsored Orphan Care applications can be assigned.');
        $this->assertNull($project->fresh()->application_id);

        // Delete the auto-created project first so we can assign it manually to $project
        \App\Models\OrphanCareProject::where('application_id', $sponsoredApp->id)->delete();

        // Attempt to assign sponsored application -> Should succeed
        $response = $this->actingAs($coo)->post("/admin/projects/{$project->id}/assign-application", [
            'application_id' => $sponsoredApp->id
        ]);
        $this->assertEquals($sponsoredApp->id, $project->fresh()->application_id);
    }

    public function test_orphan_care_project_photo_and_address_management(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_orphan_stage@rcfi.org',
            'mobile' => '9888888899',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $app = \App\Models\OrphanCareApplication::create([
            'category' => 'Orphan Care',
            'applicant_name' => 'Test Student',
            'status' => 'Approved',
            'sponsor_status' => 'Sponsored',
            'house_name' => 'Old House',
            'place' => 'Old Place',
        ]);

        $project = \App\Models\OrphanCareProject::where('application_id', $app->id)->first();
        $this->assertNotNull($project);

        $response = $this->actingAs($coo)->post("/admin/projects/orphan-care/{$project->id}/update-address", [
            'house_name' => 'New House',
            'place' => 'New Place',
            'post_office' => 'New PO',
        ]);
        $response->assertSessionHas('success', 'Student address updated successfully!');
        $this->assertEquals('New House', $app->fresh()->house_name);
        $this->assertEquals('New Place', $app->fresh()->place);

        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->image('student.jpg');

        $response = $this->actingAs($coo)->post("/admin/projects/orphan-care/{$project->id}/upload-photo", [
            'student_photo' => $file
        ]);
        $response->assertSessionHas('success', 'Student photo uploaded successfully!');
        
        $updatedApp = $app->fresh();
        $this->assertNotNull($updatedApp->student_photo);
        $this->assertFileExists(public_path($updatedApp->student_photo));

        $photoPath = public_path($updatedApp->student_photo);
        $response = $this->actingAs($coo)->delete("/admin/projects/orphan-care/{$project->id}/delete-photo");
        $response->assertSessionHas('success', 'Student photo deleted successfully!');
        $this->assertNull($app->fresh()->student_photo);
        $this->assertFileDoesNotExist($photoPath);
    }

    public function test_orphan_care_project_fund_transfers(): void
    {
        $coo = User::create([
            'name' => 'COO Test',
            'email' => 'coo_orphan_fund@rcfi.org',
            'mobile' => '9888888877',
            'role' => 2,
            'password' => bcrypt('password'),
            'designation' => 'COO',
        ]);

        $app = \App\Models\OrphanCareApplication::create([
            'category' => 'Orphan Care',
            'applicant_name' => 'Fund Test Student',
            'status' => 'Approved',
            'sponsor_status' => 'Sponsored',
        ]);

        $project = \App\Models\OrphanCareProject::where('application_id', $app->id)->first();
        $this->assertNotNull($project);

        $response = $this->actingAs($coo)->post("/admin/projects/orphan-care/{$project->id}/add-fund", [
            'date' => '2026-07-20',
            'amount' => 5000,
            'agency' => 'Agency A',
        ]);
        $response->assertSessionHas('success', 'Fund transfer record added successfully!');

        $project = $project->fresh();
        $this->assertCount(1, $project->funds);
        $this->assertEquals(5000, $project->funds->first()->amount);
        $this->assertEquals('Agency A', $project->funds->first()->agency);

        $response = $this->actingAs($coo)->delete("/admin/projects/orphan-care/{$project->id}/delete-fund/" . $project->funds->first()->id);
        $response->assertSessionHas('success', 'Fund transfer record deleted successfully!');
        $this->assertEmpty($project->fresh()->funds);
    }
}
