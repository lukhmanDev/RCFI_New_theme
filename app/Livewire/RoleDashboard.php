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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleDashboard extends Component
{
    use WithPagination, ListensForEntityChanges;

    public string $timeRange = 'this_month';
    public int $newApplicationsCount = 0;

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
        ]);
    }
}
