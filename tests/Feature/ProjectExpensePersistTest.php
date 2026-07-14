<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\HouseProject;
use App\Models\HouseApplication;
use App\Models\ProjectExpense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectExpensePersistTest extends TestCase
{
    use RefreshDatabase;

    public function test_expenses_persist_material_and_comm_indexes(): void
    {
        $pm = User::create([
            'name' => 'PM User',
            'email' => 'pm_user@rcfi.org',
            'mobile' => '9888888899',
            'role' => 3,
            'password' => bcrypt('password'),
            'designation' => 'Project Manager',
        ]);

        $app = HouseApplication::create([
            'category' => 'House',
            'applicant_name' => 'Approved Applicant',
            'status' => 'Approved',
        ]);

        $project = HouseProject::create([
            'project_id' => 'PRJ-HS-1111',
            'type_of_project' => 'House',
            'status' => 'Pending',
            'stage' => 4,
            'available_budget' => 200000,
            'application_id' => $app->id,
            'project_manager_id' => $pm->id,
        ]);

        // Add expense via route
        $response = $this->actingAs($pm)->post("/admin/projects/{$project->id}/expenses", [
            'material_index' => 2,
            'comm_index' => null,
            'expense_name' => 'Cement bags',
            'quantity' => 10,
            'amount' => 4500
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Expense added successfully!');

        // Verify database entry
        $this->assertDatabaseHas('project_expenses', [
            'project_id' => $project->id,
            'project_type' => HouseProject::class,
            'material_index' => 2,
            'comm_index' => null,
            'expense_name' => 'Cement bags',
            'quantity' => 10,
            'amount' => 4500.00,
            'type' => 'spent'
        ]);

        // Verify model attribute loading
        $expenses = $project->fresh()->expenses;
        $this->assertCount(1, $expenses);
        $this->assertEquals(2, $expenses[0]['material_index']);
        $this->assertNull($expenses[0]['comm_index']);
        $this->assertEquals('Cement bags', $expenses[0]['expense_name']);
        $this->assertEquals(10, $expenses[0]['quantity']);
        $this->assertEquals(4500.00, $expenses[0]['amount']);
    }
}
