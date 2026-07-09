<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationCenterProject;
use App\Models\CulturalCenterProject;
use App\Models\HospitalClinicProject;
use App\Models\ShopOtherProject;
use App\Models\HouseProject;

class DummyProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Available IDs from DB:
        // Users: 1=Super Admin(COO), 2=Rishad(PM), 3=rayan(Engineer), 4=Salim(Admin), 5=Lukman(Engineer)
        // Donors: 1=lukhmanul hakkeem

        $materialsA = json_encode([
            ['material' => 'Cement (50 bags)', 'amount' => 25000],
            ['material' => 'Steel Rods (200 kg)', 'amount' => 18000],
            ['material' => 'Bricks (5000 nos)', 'amount' => 15000],
            ['material' => 'Sand (10 loads)', 'amount' => 8000],
        ]);
        $materialsB = json_encode([
            ['material' => 'Roofing Sheets (30 nos)', 'amount' => 32000],
            ['material' => 'Electrical Wiring Kit', 'amount' => 12000],
            ['material' => 'Tiles (500 sqft)', 'amount' => 22000],
            ['material' => 'Paint (20 buckets)', 'amount' => 9500],
        ]);
        $materialsC = json_encode([
            ['material' => 'Plumbing Pipes Set', 'amount' => 14000],
            ['material' => 'Wooden Frames & Doors', 'amount' => 28000],
            ['material' => 'Window Glass Panels', 'amount' => 16500],
            ['material' => 'Concrete Blocks (300 nos)', 'amount' => 10000],
        ]);

        $expensesA = json_encode([
            ['description' => 'Foundation Work', 'amount' => 45000],
            ['description' => 'Labour Charges - Phase 1', 'amount' => 30000],
        ]);
        $expensesB = json_encode([
            ['description' => 'Site Clearing & Leveling', 'amount' => 18000],
            ['description' => 'Structural Steel Erection', 'amount' => 52000],
        ]);
        $expensesC = json_encode([
            ['description' => 'Masonry Work', 'amount' => 40000],
            ['description' => 'Plastering & Finishing', 'amount' => 25000],
        ]);

        $completionA = 'Foundation completed. Column work in progress. All structural members inspected and approved by site engineer.';
        $completionB = 'Roofing completed. MEP (Mechanical, Electrical and Plumbing) work ongoing. Approximately 65% project completion achieved.';
        $completionC = 'Finishing works completed. Painting and interior joinery done. Handover preparation in progress.';

        $communityA = 'Local community provided manual labour support and cleared the site. 15 volunteers contributed 3 days each.';
        $communityB = 'Community raised ₹50,000 through local fundraising events to supplement project budget.';
        $communityC = 'Beneficiary family contributed land preparation work and supplied locally available raw materials worth ₹20,000.';

        // ─────────────────────────────────────────────
        // 1. EDUCATION CENTER PROJECTS (3 records)
        // ─────────────────────────────────────────────
        $edProjects = [
            [
                'agency_project_no' => 'RCFI-ED-2025-001',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 850000.00,
                'type_of_project'   => 'Education Center',
                'remarks'           => 'Construction of primary school building with 6 classrooms and administrative block in Malappuram district.',
                'stage'             => 3,
                'status'            => 'Pending',
                'completion_details'=> $completionA,
                'community_contributions' => $communityA,
                'materials'         => $materialsA,
                'expenses'          => $expensesA,
            ],
            [
                'agency_project_no' => 'RCFI-ED-2025-002',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 1200000.00,
                'type_of_project'   => 'Education Center',
                'remarks'           => 'Renovation and expansion of existing madrasa hall to accommodate 200 students with new sanitation facilities.',
                'stage'             => 4,
                'status'            => 'Pending',
                'completion_details'=> $completionB,
                'community_contributions' => $communityB,
                'materials'         => $materialsB,
                'expenses'          => $expensesB,
            ],
            [
                'agency_project_no' => 'RCFI-ED-2025-003',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 675000.00,
                'type_of_project'   => 'Education Center',
                'remarks'           => 'Construction of computer lab and library annex to existing school in Thrissur district, benefiting 300 students.',
                'stage'             => 5,
                'status'            => 'Pending',
                'completion_details'=> $completionC,
                'community_contributions' => $communityC,
                'materials'         => $materialsC,
                'expenses'          => $expensesC,
            ],
        ];

        foreach ($edProjects as $data) {
            EducationCenterProject::create($data);
        }
        $this->command->info('Education Center: 3 projects seeded.');

        // ─────────────────────────────────────────────
        // 2. CULTURAL CENTER PROJECTS (3 records)
        // ─────────────────────────────────────────────
        $ccProjects = [
            [
                'agency_project_no' => 'RCFI-CC-2025-001',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 980000.00,
                'type_of_project'   => 'Cultural Center',
                'remarks'           => 'Construction of community cultural hall with seating for 300, stage, and green room facilities in Kozhikode.',
                'stage'             => 2,
                'status'            => 'Pending',
                'completion_details'=> $completionA,
                'community_contributions' => $communityA,
                'materials'         => $materialsB,
                'expenses'          => $expensesA,
            ],
            [
                'agency_project_no' => 'RCFI-CC-2025-002',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 1450000.00,
                'type_of_project'   => 'Cultural Center',
                'remarks'           => 'Multi-purpose community center with prayer hall, meeting rooms, and library wing in Palakkad district.',
                'stage'             => 3,
                'status'            => 'Pending',
                'completion_details'=> $completionB,
                'community_contributions' => $communityB,
                'materials'         => $materialsC,
                'expenses'          => $expensesB,
            ],
            [
                'agency_project_no' => 'RCFI-CC-2025-003',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 720000.00,
                'type_of_project'   => 'Cultural Center',
                'remarks'           => 'Renovation of heritage cultural centre building, waterproofing and interior upgrade for Eid programs in Ernakulam.',
                'stage'             => 4,
                'status'            => 'Pending',
                'completion_details'=> $completionC,
                'community_contributions' => $communityC,
                'materials'         => $materialsA,
                'expenses'          => $expensesC,
            ],
        ];

        foreach ($ccProjects as $data) {
            CulturalCenterProject::create($data);
        }
        $this->command->info('Cultural Center: 3 projects seeded.');

        // ─────────────────────────────────────────────
        // 3. HOSPITAL / CLINIC PROJECTS (3 records)
        // ─────────────────────────────────────────────
        $hcProjects = [
            [
                'agency_project_no' => 'RCFI-HC-2025-001',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 2500000.00,
                'type_of_project'   => 'Hospital or Clinics',
                'remarks'           => 'Construction of 10-bed primary healthcare clinic with outpatient department and pharmacy in Wayanad tribal area.',
                'stage'             => 3,
                'status'            => 'Pending',
                'completion_details'=> $completionA,
                'community_contributions' => $communityA,
                'materials'         => $materialsA,
                'expenses'          => $expensesA,
            ],
            [
                'agency_project_no' => 'RCFI-HC-2025-002',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 1800000.00,
                'type_of_project'   => 'Hospital or Clinics',
                'remarks'           => 'Dental and eye care clinic upgrade with modern diagnostic equipment room in Kannur district.',
                'stage'             => 4,
                'status'            => 'Pending',
                'completion_details'=> $completionB,
                'community_contributions' => $communityB,
                'materials'         => $materialsB,
                'expenses'          => $expensesB,
            ],
            [
                'agency_project_no' => 'RCFI-HC-2025-003',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 3200000.00,
                'type_of_project'   => 'Hospital or Clinics',
                'remarks'           => 'New maternity ward addition to existing community hospital with 6 beds and nursing station in Malappuram.',
                'stage'             => 5,
                'status'            => 'Pending',
                'completion_details'=> $completionC,
                'community_contributions' => $communityC,
                'materials'         => $materialsC,
                'expenses'          => $expensesC,
            ],
        ];

        foreach ($hcProjects as $data) {
            HospitalClinicProject::create($data);
        }
        $this->command->info('Hospital/Clinic: 3 projects seeded.');

        // ─────────────────────────────────────────────
        // 4. SHOPS AND OTHERS PROJECTS (3 records)
        // ─────────────────────────────────────────────
        $soProjects = [
            [
                'agency_project_no' => 'RCFI-SO-2025-001',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 450000.00,
                'type_of_project'   => 'Shops and Others',
                'remarks'           => 'Construction of 4-unit commercial shop complex to generate sustainable rental income for RCFI charitable operations in Thrissur.',
                'stage'             => 2,
                'status'            => 'Pending',
                'completion_details'=> $completionA,
                'community_contributions' => $communityA,
                'materials'         => $materialsB,
                'expenses'          => $expensesA,
            ],
            [
                'agency_project_no' => 'RCFI-SO-2025-002',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 620000.00,
                'type_of_project'   => 'Shops and Others',
                'remarks'           => 'Renovation of existing market complex with 8 shops and toilet block for local vendors in Kozhikode town area.',
                'stage'             => 3,
                'status'            => 'Pending',
                'completion_details'=> $completionB,
                'community_contributions' => $communityB,
                'materials'         => $materialsC,
                'expenses'          => $expensesB,
            ],
            [
                'agency_project_no' => 'RCFI-SO-2025-003',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 390000.00,
                'type_of_project'   => 'Shops and Others',
                'remarks'           => 'Construction of community bakery and food processing unit for women self-help group empowerment in Palakkad.',
                'stage'             => 4,
                'status'            => 'Pending',
                'completion_details'=> $completionC,
                'community_contributions' => $communityC,
                'materials'         => $materialsA,
                'expenses'          => $expensesC,
            ],
        ];

        foreach ($soProjects as $data) {
            ShopOtherProject::create($data);
        }
        $this->command->info('Shops and Others: 3 projects seeded.');

        // ─────────────────────────────────────────────
        // 5. HOUSE PROJECTS (3 records)
        // ─────────────────────────────────────────────
        $houseProjects = [
            [
                'agency_project_no' => 'RCFI-HP-2025-001',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 750000.00,
                'type_of_project'   => 'House',
                'remarks'           => 'Construction of 2BHK house (600 sqft) for a widow family of 5 who lost their home due to flood in Kottayam district.',
                'stage'             => 3,
                'status'            => 'Pending',
                'completion_details'=> $completionA,
                'community_contributions' => $communityA,
                'materials'         => $materialsA,
                'expenses'          => $expensesA,
            ],
            [
                'agency_project_no' => 'RCFI-HP-2025-002',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 5,
                'unit'              => 'RCFI',
                'available_budget'  => 550000.00,
                'type_of_project'   => 'House',
                'remarks'           => 'Reconstruction of partially collapsed house for elderly couple in Thrissur after structural damage from heavy rains.',
                'stage'             => 4,
                'status'            => 'Pending',
                'completion_details'=> $completionB,
                'community_contributions' => $communityB,
                'materials'         => $materialsB,
                'expenses'          => $expensesB,
            ],
            [
                'agency_project_no' => 'RCFI-HP-2025-003',
                'donor_id'          => 1,
                'project_manager_id'=> 2,
                'engineer_id'       => 3,
                'unit'              => 'RCFI',
                'available_budget'  => 880000.00,
                'type_of_project'   => 'House',
                'remarks'           => 'New house construction (700 sqft) for a beneficiary family with 3 children living in rented accommodation with no property in Malappuram.',
                'stage'             => 5,
                'status'            => 'Pending',
                'completion_details'=> $completionC,
                'community_contributions' => $communityC,
                'materials'         => $materialsC,
                'expenses'          => $expensesC,
            ],
        ];

        foreach ($houseProjects as $data) {
            HouseProject::create($data);
        }
        $this->command->info('House: 3 projects seeded.');

        $this->command->info('');
        $this->command->info('✅ All 15 dummy projects seeded successfully (3 per category × 5 categories).');
    }
}
