<?php

namespace Tests\Feature;

use App\Models\EducationCenterProject;
use App\Models\DrinkingWaterGroupProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Stage4ApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pm_can_submit_for_approval_at_stage_4(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_submit@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 4,
            'status' => 'Pending',
            'project_manager_id' => $pm->id,
        ]);

        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'submit',
        ]);

        $response->assertRedirect();
        $this->assertSame('Pending Approval', $project->fresh()->status);
    }

    public function test_hod_can_approve_stage_4_projects(): void
    {
        $hod = User::factory()->create([
            'role' => 4,
            'designation' => 'HOD',
            'email' => 'hod@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 4,
            'status' => 'Pending Approval',
        ]);

        $response = $this->actingAs($hod)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'approve',
        ]);

        $this->assertSame(6, $project->fresh()->stage);
        $this->assertSame('Approved', $project->fresh()->status);
    }

    public function test_coo_can_approve_legacy_projects_at_stage_less_than_4(): void
    {
        $coo = User::factory()->create([
            'role' => 2,
            'designation' => 'COO',
            'email' => 'coo_legacy@example.com',
        ]);

        // Legacy project at Stage 1
        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 1,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($coo)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'approve',
        ]);

        $this->assertSame(6, $project->fresh()->stage);
        $this->assertSame('Approved', $project->fresh()->status);
    }

    public function test_coo_or_hod_can_reject_stage_4_returning_to_stage_3(): void
    {
        $coo = User::factory()->create([
            'role' => 2,
            'designation' => 'COO',
            'email' => 'coo_reject@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 4,
            'status' => 'Pending Approval',
        ]);

        $response = $this->actingAs($coo)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'reject',
            'remarks' => 'Requires budget correction',
        ]);

        $response->assertRedirect();
        $this->assertSame(3, $project->fresh()->stage);
        $this->assertSame('Rejected', $project->fresh()->status);
        $this->assertSame('Requires budget correction', $project->fresh()->remarks);
    }

    public function test_pm_or_engineer_can_submit_corrections_from_stage_3(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_corr@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 3,
            'status' => 'Rejected',
            'project_manager_id' => $pm->id,
        ]);

        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'submit_corrections',
        ]);

        $response->assertRedirect();
        $this->assertSame(4, $project->fresh()->stage);
        $this->assertSame('Pending', $project->fresh()->status);
    }

    public function test_coo_can_finalize_stage_6_with_remarks_and_completes_project(): void
    {
        $coo = User::factory()->create([
            'role' => 2,
            'designation' => 'COO',
            'email' => 'coo_finalize@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 6,
            'status' => 'Approved',
        ]);

        // Finalization fails without Stage 6 documents
        $response = $this->actingAs($coo)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'finalize_approval',
            'remarks' => 'Excellent work.',
        ]);
        $response->assertSessionHas('error', 'Required completion documents (Stage 6) must be uploaded before final approval.');
        $this->assertSame('Approved', $project->fresh()->status);

        // Update documents
        $project->projectDocument->update([
            'completion_certificate' => 'uploads/cert.pdf',
            'measurement_book' => 'uploads/book.pdf',
        ]);

        // Finalization succeeds
        $response = $this->actingAs($coo)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'finalize_approval',
            'remarks' => 'Excellent work.',
        ]);

        $response->assertRedirect();
        $this->assertSame('Completed', $project->fresh()->status);

        $statusRecord = $project->fresh()->projectStatus;
        $this->assertNotNull($statusRecord->coo_approved_at);
        $this->assertSame($coo->id, $statusRecord->coo_approver_id);
        $this->assertSame('Excellent work.', $statusRecord->coo_remarks);
    }

    public function test_completed_project_locks_edits_for_everyone(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_lock@example.com',
        ]);

        $admin = User::factory()->create([
            'role' => 1,
            'designation' => 'Super Admin',
            'email' => 'admin_lock@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 6,
            'status' => 'Completed',
            'project_manager_id' => $pm->id,
        ]);

        // PM edit action should fail (return 403)
        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'finalize_approval',
        ]);
        $response->assertStatus(403);

        // Admin edit action should also fail (return 403)
        $response = $this->actingAs($admin)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'finalize_approval',
        ]);
        $response->assertStatus(403);
    }

    public function test_super_admin_can_reopen_completed_project(): void
    {
        $admin = User::factory()->create([
            'role' => 1,
            'designation' => 'Super Admin',
            'email' => 'admin_reopen@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 6,
            'status' => 'Completed',
        ]);

        $project->projectStatus->update([
            'coo_approved_at' => now(),
            'coo_approver_id' => $admin->id,
            'coo_remarks' => 'Completed',
        ]);

        $response = $this->actingAs($admin)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center', [
            'action' => 'reopen',
        ]);

        $response->assertRedirect();
        $this->assertSame('Approved', $project->fresh()->status);
        $this->assertSame(6, $project->fresh()->stage);

        $statusRecord = $project->fresh()->projectStatus;
        $this->assertNull($statusRecord->coo_approved_at);
        $this->assertNull($statusRecord->coo_approver_id);
        $this->assertNull($statusRecord->coo_remarks);
    }

    public function test_pm_and_engineer_can_only_see_assigned_projects(): void
    {
        $pm1 = User::factory()->create(['role' => 3, 'designation' => 'Project Manager', 'email' => 'pm1@example.com']);
        $pm2 = User::factory()->create(['role' => 3, 'designation' => 'Project Manager', 'email' => 'pm2@example.com']);

        $project1 = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 1,
            'status' => 'Pending',
            'project_manager_id' => $pm1->id,
        ]);

        $project2 = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 1,
            'status' => 'Pending',
            'project_manager_id' => $pm2->id,
        ]);

        // pm1 accesses assigned project1 -> 200 OK
        $response = $this->actingAs($pm1)->get('/admin/projects/' . $project1->id . '?type=Education%20Center');
        $response->assertStatus(200);

        // pm1 accesses unassigned project2 -> 404 Not Found (scoped out)
        $response = $this->actingAs($pm1)->get('/admin/projects/' . $project2->id . '?type=Education%20Center');
        $response->assertStatus(404);

        // HOD acts -> sees both
        $hod = User::factory()->create(['role' => 4, 'designation' => 'HOD', 'email' => 'hod_scope@example.com']);
        $response = $this->actingAs($hod)->get('/admin/projects/' . $project2->id . '?type=Education%20Center');
        $response->assertStatus(200);
    }

    public function test_pm_cannot_change_assigned_application_after_stage_4_approval(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_assign@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 6,
            'status' => 'Approved',
            'project_manager_id' => $pm->id,
        ]);

        $app = \App\Models\EducationCenterApplication::create([
            'applicant_name' => 'New App',
            'category' => 'Education Center',
            'status' => 'Approved',
        ]);

        // Attempting to change application when stage is 6 -> fails for PM
        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/assign-application?type=Education%20Center', [
            'application_id' => $app->id,
        ]);
        $response->assertSessionHas('error', 'Project Manager cannot change the assigned application after Stage 4 approval.');
    }

    public function test_only_coo_and_hod_can_update_project_status(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_status@example.com',
        ]);

        $coo = User::factory()->create([
            'role' => 2,
            'designation' => 'COO',
            'email' => 'coo_status@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 3,
            'status' => 'Pending',
            'project_manager_id' => $pm->id,
            'application_id' => 99, // dummy application id linked
        ]);

        // PM tries to update status -> fails with 403 / error message
        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/update-phase?type=Education%20Center', [
            'project_phase' => 'Foundation',
        ]);
        $response->assertSessionHas('error', 'Only COO and HOD are authorized to update project status.');

        // COO tries to update status -> succeeds
        $response = $this->actingAs($coo)->post('/admin/projects/' . $project->id . '/update-phase?type=Education%20Center', [
            'project_phase' => 'Foundation',
        ]);
        $response->assertRedirect();
        $this->assertSame('Foundation', $project->fresh()->project_phase);
    }

    public function test_category_photo_uploads_and_deletions(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_photo@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 3,
            'status' => 'Pending',
            'project_manager_id' => $pm->id,
        ]);

        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->image('project_before.jpg');

        // Upload in 'before' category
        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/upload-photo?type=Education%20Center', [
            'photo' => $file,
            'category' => 'before',
        ]);

        $response->assertRedirect();
        $this->assertCount(1, $project->fresh()->files['before'] ?? $project->fresh()->files['photos_before']);

        // Delete photo in 'before' category
        $response = $this->actingAs($pm)->delete('/admin/projects/' . $project->id . '/delete-photo/0?type=Education%20Center&category=before');
        $response->assertRedirect();
        $this->assertCount(0, $project->fresh()->files['before'] ?? $project->fresh()->files['photos_before']);
    }

    public function test_save_completion_details_with_total_project_cost(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_cost@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 6,
            'status' => 'Pending',
            'project_manager_id' => $pm->id,
        ]);

        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/completion-details?type=Education%20Center', [
            'total_project_cost' => 600000,
            'total_amount' => 500000,
            'amount_paid_by_donor' => 450000,
            'community_contribution' => 30000,
            'any_other' => 20000,
            'deductions' => 5000,
        ]);

        $response->assertRedirect();
        $details = $project->fresh()->files['completion_details'] ?? [];
        $this->assertEquals(600000, $details['total_project_cost']);
        $this->assertEquals(500000, $details['total_amount']);
    }

    public function test_assigned_applications_are_not_shown_in_the_dropdown_for_other_projects(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_drop@example.com',
        ]);

        // Create 2 applications
        $app1 = \App\Models\EducationCenterApplication::create(['applicant_name' => 'App 1', 'category' => 'Education Center', 'status' => 'Approved']);
        $app2 = \App\Models\EducationCenterApplication::create(['applicant_name' => 'App 2', 'category' => 'Education Center', 'status' => 'Approved']);

        // Project 1 has $app1 assigned
        $project1 = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 2,
            'status' => 'Pending',
            'project_manager_id' => $pm->id,
            'application_id' => $app1->id,
        ]);

        // Project 2 has no application assigned yet
        $project2 = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 2,
            'status' => 'Pending',
            'project_manager_id' => $pm->id,
        ]);

        // View project 2 -> the list of allApplications passed to the view should not contain $app1 (since it's already assigned to project 1)
        $response = $this->actingAs($pm)->get('/admin/projects/' . $project2->id . '?type=Education%20Center');
        $response->assertStatus(200);

        $viewApplications = $response->original->getData()['allApplications'];
        $appIds = $viewApplications->pluck('id')->toArray();

        $this->assertNotContains($app1->id, $appIds);
        $this->assertContains($app2->id, $appIds);

        // View project 1 -> should contain $app1 (since it's assigned to project 1 itself) and $app2 (not assigned)
        $response = $this->actingAs($pm)->get('/admin/projects/' . $project1->id . '?type=Education%20Center');
        $response->assertStatus(200);

        $viewApplications1 = $response->original->getData()['allApplications'];
        $appIds1 = $viewApplications1->pluck('id')->toArray();

        $this->assertContains($app1->id, $appIds1);
        $this->assertContains($app2->id, $appIds1);
    }

    public function test_only_hod_and_coo_can_create_projects(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_create@example.com',
        ]);

        $coo = User::factory()->create([
            'role' => 2,
            'designation' => 'COO',
            'email' => 'coo_create@example.com',
        ]);

        $donor = \App\Models\Donor::create([
            'name' => 'Test Donor',
            'type_of_partner' => 'Individual',
            'type_of_fund' => 'Grant',
        ]);

        $projectData = [
            'project_name' => 'Test Project Name',
            'sponsor' => 'Test Sponsor',
            'project_spec' => 'Test Spec',
            'agency_project_no' => 'AP-777',
            'donor_id' => $donor->id,
            'project_manager_id' => $pm->id,
            'available_budget' => 100000,
            'type_of_project' => 'Education Center',
            'redirect_category' => 'education-center',
        ];

        // PM tries to create a project -> fails
        $response = $this->actingAs($pm)->post('/admin/projects', $projectData);
        $response->assertSessionHas('error', 'Only HOD and COO are authorized to create projects.');

        // COO tries to create a project -> succeeds
        $response = $this->actingAs($coo)->post('/admin/projects', $projectData);
        $response->assertRedirect();
        $this->assertTrue(EducationCenterProject::where('agency_project_no', 'AP-777')->exists());
    }

    public function test_pm_can_update_and_delete_location_map_link(): void
    {
        $pm = User::factory()->create([
            'role' => 3,
            'designation' => 'Project Manager',
            'email' => 'pm_map@example.com',
        ]);

        $project = EducationCenterProject::create([
            'type_of_project' => 'Education Center',
            'stage' => 6,
            'status' => 'Approved',
            'project_manager_id' => $pm->id,
        ]);

        // Save map link
        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/update-map-link?type=Education%20Center', [
            'location_map_link' => 'https://maps.google.com/?q=12.34,56.78',
        ]);

        $response->assertRedirect();
        $this->assertSame('https://maps.google.com/?q=12.34,56.78', $project->fresh()->projectDocument->location_map_link);

        // Delete map link by sending empty
        $response = $this->actingAs($pm)->post('/admin/projects/' . $project->id . '/update-map-link?type=Education%20Center', [
            'location_map_link' => '',
        ]);

        $response->assertRedirect();
        $this->assertNull($project->fresh()->projectDocument->location_map_link);
    }
}
