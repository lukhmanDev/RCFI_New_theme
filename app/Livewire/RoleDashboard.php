<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Concerns\ListensForEntityChanges;
use App\Models\User;
use App\Models\Project;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Theme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleDashboard extends Component
{
    use WithPagination, ListensForEntityChanges;

    public string $timeRange = 'this_month';
    public int $newApplicationsCount = 0;
    public string $selectedState = 'Kerala';

    public function selectState(string $stateName): void
    {
        $this->selectedState = trim($stateName);
    }

    public function getThemeSummaryData(): array
    {
        $themes = Theme::with('subthemes')->get();

        $projectTables = [
            'house_projects', 'shop_other_projects', 'hospital_clinic_projects',
            'drinking_water_group_projects', 'drinking_water_individual_projects',
            'general_projects', 'education_center_projects', 'cultural_center_projects',
            'orphan_care_projects', 'differently_abled_projects', 'family_aid_projects'
        ];

        $appTables = [
            'house_applications', 'shop_other_applications', 'hospital_clinic_applications',
            'drinking_water_group_applications', 'drinking_water_individual_applications',
            'general_applications', 'education_center_applications', 'cultural_center_applications',
            'orphan_care_applications', 'differently_abled_applications', 'family_aid_applications'
        ];

        $tableThemeMap = [
            'education_center_projects' => 'Access to Quality Education',
            'education_center_applications' => 'Access to Quality Education',
            'cultural_center_projects' => 'Culture',
            'cultural_center_applications' => 'Culture',
            'drinking_water_group_projects' => 'Access to Safe Water, Sanitation and Hygiene',
            'drinking_water_individual_projects' => 'Access to Safe Water, Sanitation and Hygiene',
            'drinking_water_group_applications' => 'Access to Safe Water, Sanitation and Hygiene',
            'drinking_water_individual_applications' => 'Access to Safe Water, Sanitation and Hygiene',
            'hospital_clinic_projects' => 'Ensuring Healthy Lives and Promoting Well-being',
            'hospital_clinic_applications' => 'Ensuring Healthy Lives and Promoting Well-being',
            'house_projects' => 'Access to Adequate, Safe and Affordable Basic Services',
            'house_applications' => 'Access to Adequate, Safe and Affordable Basic Services',
            'shop_other_projects' => 'Livelihood Development',
            'shop_other_applications' => 'Livelihood Development',
            'differently_abled_projects' => 'Programme for Differently Abled',
            'differently_abled_applications' => 'Programme for Differently Abled',
            'orphan_care_projects' => 'Access to Safe, Nutritious and Sufficient Food',
            'orphan_care_applications' => 'Access to Safe, Nutritious and Sufficient Food',
            'family_aid_projects' => 'Livelihood Development',
            'family_aid_applications' => 'Livelihood Development',
            'general_projects' => 'Institutional Expenditure',
            'general_applications' => 'Institutional Expenditure',
        ];

        $themeStats = [];
        foreach ($themes as $t) {
            $tName = trim($t->name);
            $themeStats[$tName] = [
                'id' => $t->id,
                'name' => $tName,
                'subthemes_count' => $t->subthemes->count(),
                'subthemes_list' => $t->subthemes->pluck('name')->toArray(),
                'total_applications' => 0,
                'total_projects' => 0,
                'running_projects' => 0,
                'completed_projects' => 0,
                'total_budget' => 0,
                'benefited_peoples' => 0,
                'benefited_families' => 0,
            ];
        }

        foreach ($appTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $defaultTheme = $tableThemeMap[$tbl] ?? null;
            $hasThemeCol = Schema::hasColumn($tbl, 'theme');
            if ($hasThemeCol) {
                $grouped = DB::table($tbl)->select('theme', DB::raw('count(*) as aggregate'))->groupBy('theme')->get();
                foreach ($grouped as $g) {
                    $rowTheme = !empty($g->theme) ? trim($g->theme) : $defaultTheme;
                    if ($rowTheme && isset($themeStats[$rowTheme])) {
                        $themeStats[$rowTheme]['total_applications'] += (int)$g->aggregate;
                    }
                }
            } else {
                $count = DB::table($tbl)->count();
                if ($defaultTheme && isset($themeStats[$defaultTheme])) {
                    $themeStats[$defaultTheme]['total_applications'] += $count;
                }
            }
        }

        foreach ($projectTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $defaultTheme = $tableThemeMap[$tbl] ?? null;
            
            $hasTheme = Schema::hasColumn($tbl, 'theme');
            $hasStatus = Schema::hasColumn($tbl, 'status');
            $hasBudget = Schema::hasColumn($tbl, 'available_budget');
            $hasBeneficiaries = Schema::hasColumn($tbl, 'total_beneficiary_peoples');
            $hasFamilies = Schema::hasColumn($tbl, 'total_family');

            $selects = [
                DB::raw('count(*) as total_count'),
            ];
            $groupBy = [];

            if ($hasTheme) {
                $selects[] = 'theme';
                $groupBy[] = 'theme';
            }
            if ($hasStatus) {
                $selects[] = 'status';
                $groupBy[] = 'status';
            }
            if ($hasBudget) {
                $selects[] = DB::raw('sum(available_budget) as total_budget');
            }
            if ($hasBeneficiaries) {
                $selects[] = DB::raw('sum(case when status = "Completed" then total_beneficiary_peoples else 0 end) as sum_peoples');
            }
            if ($hasFamilies) {
                $selects[] = DB::raw('sum(case when status = "Completed" then total_family else 0 end) as sum_families');
            }

            $query = DB::table($tbl)->select($selects);
            if (!empty($groupBy)) {
                $query->groupBy($groupBy);
            }
            $rows = $query->get();

            foreach ($rows as $row) {
                $rowTheme = isset($row->theme) && !empty($row->theme) ? trim($row->theme) : $defaultTheme;
                if ($rowTheme && isset($themeStats[$rowTheme])) {
                    $cnt = (int)($row->total_count ?? 1);
                    $themeStats[$rowTheme]['total_projects'] += $cnt;
                    
                    $status = $row->status ?? null;
                    if ($status === 'Running') {
                        $themeStats[$rowTheme]['running_projects'] += $cnt;
                    } elseif ($status === 'Completed') {
                        $themeStats[$rowTheme]['completed_projects'] += $cnt;
                    }
                    if (isset($row->total_budget)) {
                        $themeStats[$rowTheme]['total_budget'] += (float)$row->total_budget;
                    }
                    if (isset($row->sum_peoples)) {
                        $themeStats[$rowTheme]['benefited_peoples'] += (int)$row->sum_peoples;
                    }
                    if (isset($row->sum_families)) {
                        $themeStats[$rowTheme]['benefited_families'] += (int)$row->sum_families;
                    }
                }
            }
        }

        return array_values($themeStats);
    }

    public function getStateWiseData(): array
    {
        $canonicalStates = [
            'Andaman & Nicobar Island' => 'Andaman & Nicobar Island',
            'Andhra Pradesh' => 'Andhra Pradesh',
            'Arunachal Pradesh' => 'Arunachal Pradesh',
            'Assam' => 'Assam',
            'Bihar' => 'Bihar',
            'Chandigarh' => 'Chandigarh',
            'Chhattisgarh' => 'Chhattisgarh',
            'Dadra and Nagar Haveli and Daman and Diu' => 'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi' => 'Delhi',
            'Goa' => 'Goa',
            'Gujarat' => 'Gujarat',
            'Haryana' => 'Haryana',
            'Himachal Pradesh' => 'Himachal Pradesh',
            'Jammu & Kashmir' => 'Jammu & Kashmir',
            'Jharkhand' => 'Jharkhand',
            'Karnataka' => 'Karnataka',
            'Kerala' => 'Kerala',
            'Ladakh' => 'Ladakh',
            'Lakshadweep' => 'Lakshadweep',
            'Madhya Pradesh' => 'Madhya Pradesh',
            'Maharashtra' => 'Maharashtra',
            'Manipur' => 'Manipur',
            'Meghalaya' => 'Meghalaya',
            'Mizoram' => 'Mizoram',
            'Nagaland' => 'Nagaland',
            'Odisha' => 'Odisha',
            'Puducherry' => 'Puducherry',
            'Punjab' => 'Punjab',
            'Rajasthan' => 'Rajasthan',
            'Sikkim' => 'Sikkim',
            'Tamil Nadu' => 'Tamil Nadu',
            'Telangana' => 'Telangana',
            'Tripura' => 'Tripura',
            'Uttar Pradesh' => 'Uttar Pradesh',
            'Uttarakhand' => 'Uttarakhand',
            'West Bengal' => 'West Bengal',
        ];

        // Synonyms map
        $synonyms = [
            'orissa' => 'Odisha',
            'pondicherry' => 'Puducherry',
            'andaman and nicobar' => 'Andaman & Nicobar Island',
            'andaman and nicobar islands' => 'Andaman & Nicobar Island',
            'jammu and kashmir' => 'Jammu & Kashmir',
            'j&k' => 'Jammu & Kashmir',
            'up' => 'Uttar Pradesh',
            'mp' => 'Madhya Pradesh',
            'tn' => 'Tamil Nadu',
            'wb' => 'West Bengal',
            'daman and diu' => 'Dadra and Nagar Haveli and Daman and Diu',
            'dadra & nagar haveli' => 'Dadra and Nagar Haveli and Daman and Diu',
            'dadra and nagar haveli' => 'Dadra and Nagar Haveli and Daman and Diu',
            'nct of delhi' => 'Delhi',
            'new delhi' => 'Delhi',
        ];

        $stateStats = [];
        foreach ($canonicalStates as $stName) {
            $stateStats[$stName] = [
                'name' => $stName,
                'total_projects' => 0,
                'running_projects' => 0,
                'completed_projects' => 0,
                'benefited_peoples' => 0,
                'benefited_families' => 0,
            ];
        }

        $projectTables = [
            ['project' => 'education_center_projects', 'app' => 'education_center_applications'],
            ['project' => 'cultural_center_projects', 'app' => 'cultural_center_applications'],
            ['project' => 'hospital_clinic_projects', 'app' => 'hospital_clinic_applications'],
            ['project' => 'shop_other_projects', 'app' => 'shop_other_applications'],
            ['project' => 'house_projects', 'app' => 'house_applications'],
            ['project' => 'drinking_water_individual_projects', 'app' => 'drinking_water_individual_applications'],
            ['project' => 'drinking_water_group_projects', 'app' => 'drinking_water_group_applications'],
            ['project' => 'general_projects', 'app' => 'general_applications'],
            ['project' => 'orphan_care_projects', 'app' => 'orphan_care_applications'],
            ['project' => 'differently_abled_projects', 'app' => 'differently_abled_applications'],
            ['project' => 'family_aid_projects', 'app' => 'family_aid_applications'],
            ['project' => 'projects', 'app' => 'applications'],
        ];

        foreach ($projectTables as $pair) {
            $pTbl = $pair['project'];
            $aTbl = $pair['app'];
            if (!Schema::hasTable($pTbl)) continue;

            $hasAppId = Schema::hasColumn($pTbl, 'application_id') && Schema::hasTable($aTbl);
            $hasStateInProject = Schema::hasColumn($pTbl, 'state');

            $query = DB::table($pTbl);
            $hasAppState = false;
            $hasAppLocState = false;
            $hasAppMeta = false;

            if ($hasAppId) {
                $selects = ["$pTbl.*"];
                $hasAppState = Schema::hasColumn($aTbl, 'state');
                $hasAppLocState = Schema::hasColumn($aTbl, 'locality_state');
                $hasAppMeta = Schema::hasColumn($aTbl, 'meta');

                if ($hasAppState) {
                    $selects[] = "$aTbl.state as app_state";
                }
                if ($hasAppLocState) {
                    $selects[] = "$aTbl.locality_state as app_locality_state";
                }
                if ($hasAppMeta) {
                    $selects[] = "$aTbl.meta as app_meta";
                }

                $query->leftJoin($aTbl, "$pTbl.application_id", '=', "$aTbl.id")
                      ->select($selects);
            }
            $projects = $query->get();

            foreach ($projects as $p) {
                $rawState = null;
                if ($hasStateInProject && !empty($p->state)) {
                    $rawState = $p->state;
                } elseif ($hasAppId) {
                    if (!empty($p->app_state)) {
                        $rawState = $p->app_state;
                    } elseif (!empty($p->app_locality_state)) {
                        $rawState = $p->app_locality_state;
                    } elseif (!empty($p->app_meta)) {
                        $metaArr = is_string($p->app_meta) ? json_decode($p->app_meta, true) : (array)$p->app_meta;
                        $rawState = $metaArr['state'] ?? ($metaArr['locality_state'] ?? null);
                    }
                }

                if (!$rawState) {
                    $rawState = 'Kerala';
                }

                $normKey = strtolower(trim($rawState));
                $targetState = $synonyms[$normKey] ?? null;

                if (!$targetState) {
                    foreach ($canonicalStates as $canon) {
                        if (strcasecmp($canon, $normKey) === 0 || strcasecmp($canon, $rawState) === 0) {
                            $targetState = $canon;
                            break;
                        }
                    }
                }

                if (!$targetState) {
                    $targetState = 'Kerala';
                }

                if (!isset($stateStats[$targetState])) {
                    $stateStats[$targetState] = [
                        'name' => $targetState,
                        'total_projects' => 0,
                        'running_projects' => 0,
                        'completed_projects' => 0,
                        'benefited_peoples' => 0,
                        'benefited_families' => 0,
                    ];
                }

                $stateStats[$targetState]['total_projects']++;
                $st = $p->status ?? 'Active';
                $isCompleted = ($st === 'Completed');

                if (in_array($st, ['Running', 'Active', 'Approved', 'Ongoing', 'In Progress'])) {
                    $stateStats[$targetState]['running_projects']++;
                } elseif ($isCompleted) {
                    $stateStats[$targetState]['completed_projects']++;
                }

                if ($isCompleted) {
                    $peoples = (int) ($p->total_beneficiary_peoples ?? ($p->benefited_peoples ?? 0));
                    $families = (int) ($p->total_family ?? ($p->benefited_families ?? 0));

                    $stateStats[$targetState]['benefited_peoples'] += $peoples;
                    $stateStats[$targetState]['benefited_families'] += $families;
                }
            }
        }

        return $stateStats;
    }

    public function onNewApplication(array $payload = []): void
    {
        $this->newApplicationsCount++;
        $this->dispatch('$refresh');
    }

    public function watchedEvents(): array
    {
        return [
            'application.created',
            'application.updated',
            'application.deleted',
            'application.approved',
            'application.rejected',
            'project.created',
            'project.updated',
            'project.deleted',
            'leaverequest.created',
            'leaverequest.updated',
            'leaverequest.approved',
            'leaverequest.rejected',
        ];
    }

    public function setTimeRange(string $range): void
    {
        $this->timeRange = $range;
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Real-time Application Counts
        $appTables = [
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'house_applications',
            'drinking_water_individual_applications',
            'drinking_water_group_applications',
            'general_applications',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'applications',
        ];

        $totalApplications = 0;
        $pendingCount = 0;
        $approvedCount = 0;
        $underReviewCount = 0;
        $rejectedCount = 0;

        foreach ($appTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $totalApplications += DB::table($tbl)->count();
            if (Schema::hasColumn($tbl, 'status')) {
                $pendingCount += DB::table($tbl)->where('status', 'Pending')->count();
                $approvedCount += DB::table($tbl)->where('status', 'Approved')->count();
                $underReviewCount += DB::table($tbl)->whereIn('status', ['Under Review', 'In Review', 'Processing'])->count();
                $rejectedCount += DB::table($tbl)->where('status', 'Rejected')->count();
            }
        }
        
        // Real-time Project Counts & Beneficiary Totals across all project categories
        $projectTables = [
            'education_center_projects',
            'cultural_center_projects',
            'hospital_clinic_projects',
            'shop_other_projects',
            'house_projects',
            'drinking_water_individual_projects',
            'drinking_water_group_projects',
            'general_projects',
            'orphan_care_projects',
            'differently_abled_projects',
            'family_aid_projects',
            'projects',
        ];

        $totalProjects = 0;
        $runningProjects = 0;
        $completedProjects = 0;
        $myAssignedProjects = collect();
        $myRunningProjects = 0;
        $myCompletedProjects = 0;
        $totalBeneficiaryPeoples = 0;
        $totalBeneficiaryFamily = 0;

        $currentYear = (int) now()->format('Y');
        $yearLabels = [];
        $yearPeoplesMap = array_fill_keys(range($currentYear - 4, $currentYear), 0);
        $yearFamiliesMap = array_fill_keys(range($currentYear - 4, $currentYear), 0);

        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $yearLabels[] = (string)$y;
        }

        foreach ($projectTables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;

            $totalProjects += DB::table($tbl)->count();

            if (Schema::hasColumn($tbl, 'status')) {
                $runningProjects += DB::table($tbl)->whereIn('status', ['Running', 'Active', 'Approved'])->count();
                $completedProjects += DB::table($tbl)->where('status', 'Completed')->count();
            }

            if (Schema::hasColumn($tbl, 'total_beneficiary_peoples')) {
                $totalBeneficiaryPeoples += (int) DB::table($tbl)
                    ->where('status', 'Completed')
                    ->whereNotNull('total_beneficiary_peoples')
                    ->sum('total_beneficiary_peoples');
            }
            if (Schema::hasColumn($tbl, 'total_family')) {
                $totalBeneficiaryFamily += (int) DB::table($tbl)
                    ->where('status', 'Completed')
                    ->whereNotNull('total_family')
                    ->sum('total_family');
            }

            // User assigned
            if (Schema::hasColumn($tbl, 'project_manager_id') || Schema::hasColumn($tbl, 'engineer_id') || Schema::hasColumn($tbl, 'manager_id')) {
                $myProjects = DB::table($tbl)->where(function($q) use ($user, $tbl) {
                    if (Schema::hasColumn($tbl, 'project_manager_id')) $q->orWhere('project_manager_id', $user->id);
                    if (Schema::hasColumn($tbl, 'manager_id')) $q->orWhere('manager_id', $user->id);
                    if (Schema::hasColumn($tbl, 'engineer_id')) $q->orWhere('engineer_id', $user->id);
                })->get();

                foreach ($myProjects as $mp) {
                    $myAssignedProjects->push($mp);
                    $st = $mp->status ?? 'Active';
                    if (in_array($st, ['Running', 'Active', 'Approved'])) $myRunningProjects++;
                    if ($st === 'Completed') $myCompletedProjects++;
                }
            }

            // Year-wise sums (only completed projects)
            if (Schema::hasColumn($tbl, 'created_at')) {
                for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                    if (Schema::hasColumn($tbl, 'total_beneficiary_peoples')) {
                        $yearPeoplesMap[$y] += (int) DB::table($tbl)
                            ->where('status', 'Completed')
                            ->whereYear('created_at', $y)
                            ->whereNotNull('total_beneficiary_peoples')
                            ->sum('total_beneficiary_peoples');
                    }
                    if (Schema::hasColumn($tbl, 'total_family')) {
                        $yearFamiliesMap[$y] += (int) DB::table($tbl)
                            ->where('status', 'Completed')
                            ->whereYear('created_at', $y)
                            ->whereNotNull('total_family')
                            ->sum('total_family');
                    }
                }
            }
        }

        $beneficiaryChartData = [
            'labels'   => $yearLabels,
            'peoples'  => array_values($yearPeoplesMap),
            'families' => array_values($yearFamiliesMap),
        ];

        // Leave Balances & Status for current user
        $year = now()->year;
        $activeTypes = LeaveType::where('is_active', true)->get();
        $myLeaveBalances = collect();

        foreach ($activeTypes as $type) {
            if ($user->isEligibleFor($type)) {
                $yearVal = $type->accrual_type === 'OneTime' ? null : $year;
                $bal = LeaveBalance::where('user_id', $user->id)
                    ->where('leave_type_id', $type->id)
                    ->where(function($q) use ($yearVal) {
                        if ($yearVal) {
                            $q->where('year', $yearVal)->orWhereNull('year');
                        } else {
                            $q->whereNull('year');
                        }
                    })->first();

                if (!$bal) {
                    $bal = LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $type->id,
                        'year' => $yearVal,
                        'allocated_days' => $type->max_days_per_year ?? $type->max_days_lifetime ?? 0,
                        'used_days' => 0,
                        'carried_forward_days' => 0,
                    ]);
                }

                $myLeaveBalances->push($bal);
            }
        }

        $currentLeave = $user->current_leave;

        // My Recent Leave Applications History
        $myRecentLeaveRequests = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Pending Leave Requests for Approver Roles
        $pendingLeaveApprovals = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stateSummary = $this->getStateWiseData();
        $selectedStateInfo = $stateSummary[$this->selectedState] ?? [
            'name'               => $this->selectedState,
            'total_projects'     => 0,
            'running_projects'   => 0,
            'completed_projects' => 0,
            'benefited_peoples'  => 0,
            'benefited_families' => 0,
        ];

        return view('livewire.role-dashboard', [
            'user'                     => $user,
            'newApplicationsCount'     => $this->newApplicationsCount ?? 0,
            'totalApplications'        => $totalApplications,
            'pendingCount'             => $pendingCount,
            'approvedCount'            => $approvedCount,
            'underReviewCount'         => $underReviewCount,
            'rejectedCount'            => $rejectedCount,
            'totalProjects'            => $totalProjects,
            'runningProjects'          => $runningProjects,
            'completedProjects'        => $completedProjects,
            'myAssignedProjects'       => $myAssignedProjects,
            'myRunningProjects'        => $myRunningProjects,
            'myCompletedProjects'      => $myCompletedProjects,
            'myLeaveBalances'          => $myLeaveBalances,
            'currentLeave'             => $currentLeave,
            'myRecentLeaveRequests'    => $myRecentLeaveRequests,
            'pendingLeaveApprovals'    => $pendingLeaveApprovals,
            'totalBeneficiaryPeoples'  => $totalBeneficiaryPeoples,
            'totalBeneficiaryFamily'   => $totalBeneficiaryFamily,
            'beneficiaryChartData'     => $beneficiaryChartData,
            'themeSummaryData'         => $this->getThemeSummaryData(),
            'stateSummaryData'         => $stateSummary,
            'selectedState'            => $this->selectedState,
            'selectedStateInfo'        => $selectedStateInfo,
        ]);
    }
}
