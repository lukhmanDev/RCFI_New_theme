<?php

namespace Tests\Feature;

use App\Models\EducationCenterProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Stage4ApprovalTest extends TestCase
{
    use RefreshDatabase;

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
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($hod)->post('/admin/projects/' . $project->id . '/approve?type=Education%20Center');

        $response->assertRedirect();
        $this->assertSame(5, $project->fresh()->stage);
    }
}
