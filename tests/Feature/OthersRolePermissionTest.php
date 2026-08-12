<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OthersRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_others_role_cannot_add_applications(): void
    {
        $othersUser = User::factory()->create([
            'role' => 'others',
        ]);

        $response = $this->actingAs($othersUser)->postJson(route('applications.store'), [
            'applicant_name' => 'John Doe',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error' => 'Users with role "Others" cannot add applications.',
        ]);
    }

    public function test_super_admin_can_add_applications(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($superAdmin)->post(route('applications.store'), [
            'applicant_name' => 'Jane Doe',
            'category' => 'General',
            'status' => 'Pending',
        ]);

        $response->assertStatus(302); // Redirects back or to category page on success
    }

    public function test_others_role_does_not_see_master_data(): void
    {
        $othersUser = User::factory()->create([
            'role' => 'others',
        ]);

        $response = $this->actingAs($othersUser)->get(route('admin.home'));
        $response->assertStatus(200);
        $response->assertDontSee('Master Data');

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $adminResponse = $this->actingAs($superAdmin)->get(route('admin.home'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Master Data');
    }

    public function test_only_super_admin_and_coo_can_edit_or_delete_themes_and_contractors(): void
    {
        $theme = \App\Models\Theme::create(['name' => 'Education', 'status' => 1]);
        $subtheme = \App\Models\Subtheme::create(['theme_id' => $theme->id, 'name' => 'Primary School', 'status' => 1]);
        $contractor = \App\Models\Contractor::create([
            'name' => 'John Builder',
            'company_name' => 'Builder Co',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);

        $pmUser = User::factory()->create(['role' => 'pm']);
        $cooUser = User::factory()->create(['role' => 'coo']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        // PM cannot edit theme, subtheme, or contractor
        $this->actingAs($pmUser)->put(route('themes.update', $theme->id), ['name' => 'Updated Theme'])->assertSessionHas('error');
        $this->actingAs($pmUser)->delete(route('themes.destroy', $theme->id))->assertSessionHas('error');

        $this->actingAs($pmUser)->put(route('subthemes.update', $subtheme->id), ['theme_id' => $theme->id, 'name' => 'Updated Subtheme'])->assertSessionHas('error');
        $this->actingAs($pmUser)->delete(route('subthemes.destroy', $subtheme->id))->assertSessionHas('error');

        $this->actingAs($pmUser)->put(route('contractors.update', $contractor->id), [
            'name' => 'Updated Contractor',
            'company_name' => 'Builder Co',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ])->assertSessionHas('error');
        $this->actingAs($pmUser)->delete(route('contractors.destroy', $contractor->id))->assertSessionHas('error');

        // COO can edit theme, subtheme, contractor
        $this->actingAs($cooUser)->put(route('themes.update', $theme->id), ['name' => 'Theme By COO'])->assertSessionHas('success');
        $this->actingAs($cooUser)->put(route('subthemes.update', $subtheme->id), ['theme_id' => $theme->id, 'name' => 'Subtheme By COO'])->assertSessionHas('success');
        $this->actingAs($cooUser)->put(route('contractors.update', $contractor->id), [
            'name' => 'Contractor By COO',
            'company_name' => 'Builder Co',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ])->assertSessionHas('success');

        // Super Admin can delete theme, subtheme, contractor
        $this->actingAs($superAdmin)->delete(route('subthemes.destroy', $subtheme->id))->assertSessionHas('success');
        $this->actingAs($superAdmin)->delete(route('themes.destroy', $theme->id))->assertSessionHas('success');
        $this->actingAs($superAdmin)->delete(route('contractors.destroy', $contractor->id))->assertSessionHas('success');
    }
}
