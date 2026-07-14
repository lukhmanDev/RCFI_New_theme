<?php

namespace Tests\Unit;

use App\Models\ProjectDocument;
use App\Traits\HasProjectColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectStatusDefaultTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->string('status')->nullable();
            $table->string('status_custom')->nullable();
            $table->timestamps();
        });

        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');

            foreach (array_unique(ProjectDocument::$docColumnMap) as $column) {
                $table->string($column)->nullable();
                $table->timestamp($column . '_ticked_at')->nullable();
            }

            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('project_documents');
        Schema::dropIfExists('project_statuses');
        Schema::dropIfExists('test_projects');

        parent::tearDown();
    }

    public function test_creating_a_project_does_not_assign_project_assigned_status_by_default(): void
    {
        $project = TestProject::create(['name' => 'Sample Project']);

        $this->assertNotNull($project->projectStatus);
        $this->assertNull($project->projectStatus->status);
        $this->assertSame('', $project->project_phase);
    }
}

class TestProject extends Model
{
    use HasProjectColumns;

    protected $table = 'test_projects';
    protected $guarded = [];
}
