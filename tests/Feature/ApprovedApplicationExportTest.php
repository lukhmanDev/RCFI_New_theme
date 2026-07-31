<?php

namespace Tests\Feature;

use App\Models\Cluster;
use App\Models\OrphanCareApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovedApplicationExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_applications_excel_export()
    {
        $superAdmin = User::factory()->create(['role' => 1]);
        $cluster = Cluster::create(['name' => 'North Region Cluster', 'code' => 'NRC-01']);

        $sponsoredApp = OrphanCareApplication::create([
            'applicant_name' => 'Orphan A',
            'status' => 'Approved',
            'sponsor_status' => 'Sponsored',
            'cluster_id' => $cluster->id,
            'agency_number' => 'AG-1001',
            'meta' => [
                'father_name' => 'Late John',
                'mother_name' => 'Mary',
                'guardian_name' => 'Mary'
            ]
        ]);

        $unsponsoredApp = OrphanCareApplication::create([
            'applicant_name' => 'Orphan B',
            'status' => 'Approved',
            'sponsor_status' => 'Not Sponsored',
            'cluster_id' => $cluster->id,
            'agency_number' => 'AG-1002',
            'meta' => [
                'father_name' => 'Late David',
                'mother_name' => 'Sarah'
            ]
        ]);

        // Test export all approved
        $response = $this->actingAs($superAdmin)->get(route('applications.approved.export', [
            'category' => 'orphan-care'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Application ID', $content);
        $this->assertStringContainsString('Father Name', $content);
        $this->assertStringContainsString('Orphan A', $content);
        $this->assertStringContainsString('Orphan B', $content);

        // Test export with sponsored filter
        $responseSponsored = $this->actingAs($superAdmin)->get(route('applications.approved.export', [
            'category' => 'orphan-care',
            'sponsor_status' => 'sponsored'
        ]));

        $sponsoredContent = $responseSponsored->streamedContent();
        $this->assertStringContainsString('Orphan A', $sponsoredContent);
        $this->assertStringNotContainsString('Orphan B', $sponsoredContent);

        // Test export with cluster filter
        $responseCluster = $this->actingAs($superAdmin)->get(route('applications.approved.export', [
            'category' => 'orphan-care',
            'cluster_id' => $cluster->id
        ]));

        $clusterContent = $responseCluster->streamedContent();
        $this->assertStringContainsString('North Region Cluster', $clusterContent);
    }
}
