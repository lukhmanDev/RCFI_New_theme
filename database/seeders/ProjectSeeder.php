<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donor;
use App\Models\User;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or Find Donors
        $donor1 = Donor::firstOrCreate(
            ['email' => 'afsal@example.com'],
            [
                'name' => 'Afsal',
                'short_name' => 'AFSL',
                'website' => 'http://afsal.example.com',
                'type_of_partner' => 'Individual',
                'type_of_fund' => 'One-time',
                'contact_person' => 'Afsal',
                'phone' => '1234567890'
            ]
        );

        $donor2 = Donor::firstOrCreate(
            ['email' => 'riswan@example.com'],
            [
                'name' => 'Riswan',
                'short_name' => 'RSWN',
                'website' => 'http://riswan.example.com',
                'type_of_partner' => 'Individual',
                'type_of_fund' => 'Monthly',
                'contact_person' => 'Riswan',
                'phone' => '1234567891'
            ]
        );

        $donor3 = Donor::firstOrCreate(
            ['email' => 'rayan@example.com'],
            [
                'name' => 'Rayan',
                'short_name' => 'RYN',
                'website' => 'http://rayan.example.com',
                'type_of_partner' => 'Corporate',
                'type_of_fund' => 'Annual',
                'contact_person' => 'Rayan',
                'phone' => '1234567892'
            ]
        );

        // 2. Create Project Managers (Users)
        $manager = User::firstOrCreate(
            ['email' => 'rayan.s@example.com'],
            [
                'name' => 'Rayan.s',
                'mobile' => 8888888888,
                'role' => 3, // Project Manager
                'password' => bcrypt('password'),
                'designation' => 'Project Manager'
            ]
        );

        // 3. Clear existing projects in all 11 tables
        \App\Models\EducationCenterProject::truncate();
        \App\Models\CulturalCenterProject::truncate();
        \App\Models\HospitalClinicProject::truncate();
        \App\Models\ShopOtherProject::truncate();
        \App\Models\HouseProject::truncate();
        \App\Models\DrinkingWaterGroupProject::truncate();
        \App\Models\DrinkingWaterIndividualProject::truncate();
        \App\Models\OrphanCareProject::truncate();
        \App\Models\DifferentlyAbledProject::truncate();
        \App\Models\FamilyAidProject::truncate();
        \App\Models\GeneralProject::truncate();

        // 4. Seed Construction Projects (6)
        \App\Models\EducationCenterProject::create([
            'agency_project_no' => 'awesdqw',
            'donor_id' => $donor2->id, // Riswan
            'project_manager_id' => $manager->id, // Rayan.s
            'available_budget' => 100000.00,
            'type_of_project' => 'Education Center',
            'remarks' => 'wer',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        \App\Models\EducationCenterProject::create([
            'agency_project_no' => 'AgECpro4',
            'donor_id' => $donor1->id, // Afsal
            'project_manager_id' => $manager->id,
            'available_budget' => 100000.00,
            'type_of_project' => 'Education Center',
            'remarks' => 'nill',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        \App\Models\EducationCenterProject::create([
            'agency_project_no' => 'RCFI25EC040',
            'donor_id' => $donor1->id, // Afsal
            'project_manager_id' => $manager->id,
            'available_budget' => 30000.00,
            'type_of_project' => 'Education Center',
            'remarks' => 'Hai',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        \App\Models\CulturalCenterProject::create([
            'agency_project_no' => 'CC-PROJ-01',
            'donor_id' => $donor3->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 150000.00,
            'type_of_project' => 'Cultural Center',
            'remarks' => 'Renovation of center',
            'stage' => 2,
            'status' => 'Pending'
        ]);

        \App\Models\HospitalClinicProject::create([
            'agency_project_no' => 'HC-PROJ-01',
            'donor_id' => $donor2->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 250000.00,
            'type_of_project' => 'Hospital or Clinics',
            'remarks' => 'Equipment support',
            'stage' => 3,
            'status' => 'Pending'
        ]);

        \App\Models\HouseProject::create([
            'agency_project_no' => 'HS-PROJ-01',
            'donor_id' => $donor1->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 90000.00,
            'type_of_project' => 'House',
            'remarks' => 'New build support',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        // 5. Seed Sweet Water Projects (1)
        \App\Models\DrinkingWaterGroupProject::create([
            'agency_project_no' => 'DWG-PROJ-01',
            'donor_id' => $donor2->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 45000.00,
            'type_of_project' => 'Drinking Water - Group Level',
            'remarks' => 'Borewell installation',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        // 6. Seed Orphan Care Projects (2)
        \App\Models\OrphanCareProject::create([
            'agency_project_no' => 'OC-PROJ-01',
            'donor_id' => $donor1->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 50000.00,
            'type_of_project' => 'Orphan Care',
            'remarks' => 'Annual educational aids',
            'stage' => 4,
            'status' => 'Pending'
        ]);
        \App\Models\OrphanCareProject::create([
            'agency_project_no' => 'OC-PROJ-02',
            'donor_id' => $donor3->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 35000.00,
            'type_of_project' => 'Orphan Care',
            'remarks' => 'Health checkup sponsorship',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        // 7. Seed Differently Abled Projects (2)
        \App\Models\DifferentlyAbledProject::create([
            'agency_project_no' => 'DA-PROJ-01',
            'donor_id' => $donor2->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 60000.00,
            'type_of_project' => 'Differently Abled',
            'remarks' => 'Prosthetic limbs provision',
            'stage' => 2,
            'status' => 'Pending'
        ]);
        \App\Models\DifferentlyAbledProject::create([
            'agency_project_no' => 'DA-PROJ-02',
            'donor_id' => $donor1->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 40000.00,
            'type_of_project' => 'Differently Abled',
            'remarks' => 'Wheelchairs distribution',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        // 8. Seed Family Aid Projects (1)
        \App\Models\FamilyAidProject::create([
            'agency_project_no' => 'FA-PROJ-01',
            'donor_id' => $donor3->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 70000.00,
            'type_of_project' => 'Family Aid',
            'remarks' => 'Livelihood support kits',
            'stage' => 1,
            'status' => 'Pending'
        ]);

        // 9. Seed General Projects (1)
        \App\Models\GeneralProject::create([
            'agency_project_no' => 'GN-PROJ-01',
            'donor_id' => $donor1->id,
            'project_manager_id' => $manager->id,
            'available_budget' => 30000.00,
            'type_of_project' => 'General',
            'remarks' => 'Community welfare program',
            'stage' => 1,
            'status' => 'Pending'
        ]);
    }
}
