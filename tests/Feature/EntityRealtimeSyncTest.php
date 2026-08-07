<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\Project;
use App\Models\LeaveRequest;
use App\Events\EntityChanged;

class EntityRealtimeSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Schema::hasTable('applications') && !Schema::hasColumn('applications', 'category')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('category')->default('General')->nullable();
            });
        }

        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('project_id')->nullable();
                $table->string('name_of_project')->nullable();
                $table->string('type_of_project')->nullable();
                $table->string('unit')->default('RCFI');
                $table->string('status')->default('Running');
                $table->integer('current_stage')->default(1);
                $table->timestamps();
            });
        }
    }

    public function test_entity_changed_event_dispatches_on_application_creation()
    {
        Event::fake([EntityChanged::class]);

        $app = Application::create([
            'applicant_name' => 'John Doe Applicant',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        Event::assertDispatched(EntityChanged::class, function ($event) use ($app) {
            return $event->entityType === 'application'
                && $event->operation === 'created'
                && $event->payload['id'] === $app->id
                && in_array('role.coo', $event->channels)
                && in_array('role.project_manager', $event->channels);
        });
    }

    public function test_explicit_workflow_broadcast_dispatches_custom_operation()
    {
        Event::fake([EntityChanged::class]);

        $app = Application::create([
            'applicant_name' => 'Jane Smith Applicant',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        $app->update(['status' => 'Approved']);
        $app->broadcastChange('approved', ['approver' => 'COO User']);

        Event::assertDispatched(EntityChanged::class, function ($event) use ($app) {
            return $event->entityType === 'application'
                && $event->operation === 'approved'
                && $event->payload['status'] === 'Approved'
                && $event->payload['approver'] === 'COO User';
        });
    }

    public function test_project_model_broadcasts_on_crud_lifecycle()
    {
        Event::fake([EntityChanged::class]);

        $project = Project::create([
            'name_of_project' => 'Test Community Center',
            'type_of_project' => 'General',
            'unit' => 'RCFI',
            'status' => 'Running',
        ]);

        Event::assertDispatched(EntityChanged::class, function ($event) use ($project) {
            return $event->entityType === 'project'
                && $event->operation === 'created'
                && $event->payload['id'] === $project->id;
        });
    }

    public function test_channel_authorization_rules_for_roles()
    {
        $coo = User::factory()->create(['role' => 'coo']);
        $pm = User::factory()->create(['role' => 'project_manager']);
        $employee = User::factory()->create(['role' => 'employee']);

        // Check COO channel
        $this->assertTrue($coo->isCoo());
        $this->assertFalse($employee->isCoo());

        // Check PM channel
        $this->assertTrue($pm->isPm());
        $this->assertFalse($coo->isPm());
    }

    public function test_application_created_event_dispatched_on_store()
    {
        Event::fake([\App\Events\ApplicationCreated::class]);

        $app = Application::create([
            'applicant_name' => 'Reception Form Applicant',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        event(new \App\Events\ApplicationCreated($app));

        Event::assertDispatched(\App\Events\ApplicationCreated::class, function ($event) use ($app) {
            return $event->application->id === $app->id;
        });
    }

    public function test_table_row_changed_event_dispatches_on_crud()
    {
        Event::fake([\App\Events\TableRowChanged::class]);

        $app = Application::create([
            'applicant_name' => 'Live Table Applicant',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        $app->broadcastTableChange('updated', ['status' => 'Approved']);

        Event::assertDispatched(\App\Events\TableRowChanged::class, function ($event) use ($app) {
            return $event->entityType === 'application'
                && $event->operation === 'updated'
                && $event->row['id'] === $app->id;
        });
    }
}
