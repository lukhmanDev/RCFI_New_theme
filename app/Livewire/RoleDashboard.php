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
                $rows = DB::table($tbl)->get(['theme']);
                foreach ($rows as $row) {
                    $rowTheme = !empty($row->theme) ? trim($row->theme) : $defaultTheme;
                    if ($rowTheme && isset($themeStats[$rowTheme])) {
                        $themeStats[$rowTheme]['total_applications']++;
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
            
            $colsToSelect = [];
            if (Schema::hasColumn($tbl, 'theme')) $colsToSelect[] = 'theme';
            if (Schema::hasColumn($tbl, 'status')) $colsToSelect[] = 'status';
            if (Schema::hasColumn($tbl, 'available_budget')) $colsToSelect[] = 'available_budget';
            if (Schema::hasColumn($tbl, 'total_beneficiary_peoples')) $colsToSelect[] = 'total_beneficiary_peoples';
            if (Schema::hasColumn($tbl, 'total_family')) $colsToSelect[] = 'total_family';

            $rows = empty($colsToSelect) ? DB::table($tbl)->get() : DB::table($tbl)->get($colsToSelect);

            foreach ($rows as $row) {
                $rowTheme = isset($row->theme) && !empty($row->theme) ? trim($row->theme) : $defaultTheme;
                if ($rowTheme && isset($themeStats[$rowTheme])) {
                    $themeStats[$rowTheme]['total_projects']++;
                    if (isset($row->status)) {
                        if ($row->status === 'Running') $themeStats[$rowTheme]['running_projects']++;
                        if ($row->status === 'Completed') $themeStats[$rowTheme]['completed_projects']++;
                    }
                    if (isset($row->available_budget)) {
                        $themeStats[$rowTheme]['total_budget'] += (float)$row->available_budget;
                    }
                    if (isset($row->total_beneficiary_peoples)) {
                        $themeStats[$rowTheme]['benefited_peoples'] += (int)$row->total_beneficiary_peoples;
                    }
                    if (isset($row->total_family)) {
                        $themeStats[$rowTheme]['benefited_families'] += (int)$row->total_family;
                    }
                }
            }
        }

        return array_values($themeStats);
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
        $hasApps = \Illuminate\Support\Facades\Schema::hasTable('applications');
        $totalApplications = $hasApps ? DB::table('applications')->count() : 0;
        $pendingCount = $hasApps ? DB::table('applications')->where('status', 'Pending')->count() : 0;
        $approvedCount = $hasApps ? DB::table('applications')->where('status', 'Approved')->count() : 0;
        $underReviewCount = $hasApps ? DB::table('applications')->where('status', 'Under Review')->count() : 0;
        $rejectedCount = $hasApps ? DB::table('applications')->where('status', 'Rejected')->count() : 0;
        
        // Real-time Project Counts
        $hasProjects = \Illuminate\Support\Facades\Schema::hasTable('projects');
        $totalProjects = $hasProjects ? Project::count() : 0;
        $runningProjects = $hasProjects ? Project::where('status', 'Running')->count() : 0;
        $completedProjects = $hasProjects ? Project::where('status', 'Completed')->count() : 0;

        // My assigned projects
        $myAssignedProjects = $hasProjects ? Project::where('engineer_id', $user->id)
            ->orWhere('project_manager_id', $user->id)
            ->get() : collect();

        $myRunningProjects = $myAssignedProjects->where('status', 'Running')->count();
        $myCompletedProjects = $myAssignedProjects->where('status', 'Completed')->count();

        // ── Beneficiary & Family Totals ──────────────────────────────────────────
        $totalBeneficiaryPeoples = 0;
        $totalBeneficiaryFamily  = 0;
        if ($hasProjects && Schema::hasColumn('projects', 'total_beneficiary_peoples')) {
            $totalBeneficiaryPeoples = (int) Project::whereNotNull('total_beneficiary_peoples')
                ->sum('total_beneficiary_peoples');
            $totalBeneficiaryFamily  = (int) Project::whereNotNull('total_family')
                ->sum('total_family');
        }

        // ── Year-wise chart (last 5 years) ───────────────────────────────────────
        $currentYear = (int) now()->format('Y');
        $yearLabels  = [];
        $yearPeoples = [];
        $yearFamilies = [];
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $yearLabels[] = (string) $y;
            if ($hasProjects && Schema::hasColumn('projects', 'total_beneficiary_peoples')) {
                $yearPeoples[]  = (int) Project::whereYear('created_at', $y)
                    ->whereNotNull('total_beneficiary_peoples')
                    ->sum('total_beneficiary_peoples');
                $yearFamilies[] = (int) Project::whereYear('created_at', $y)
                    ->whereNotNull('total_family')
                    ->sum('total_family');
            } else {
                $yearPeoples[]  = 0;
                $yearFamilies[] = 0;
            }
        }
        $beneficiaryChartData = [
            'labels'   => $yearLabels,
            'peoples'  => $yearPeoples,
            'families' => $yearFamilies,
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

        return view('livewire.role-dashboard', [
            'user'                     => $user,
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
        ]);
    }
}
