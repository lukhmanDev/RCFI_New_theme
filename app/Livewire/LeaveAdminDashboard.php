<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveAdminDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'pending';

    // Reject Modal state
    public bool $showRejectModal = false;
    public ?int $rejectRequestId = null;
    public string $rejectRemarks = '';

    // Calendar state
    public int $calendarMonth;
    public int $calendarYear;

    // Report state
    public string $reportStatus = '';
    public string $reportLeaveType = '';
    public string $reportStartDate = '';
    public string $reportEndDate = '';

    // Leave type edit state
    public ?int $editingTypeId = null;
    public float $editMaxYear = 0;
    public float $editMaxLifetime = 0;
    public int $editMinService = 0;

    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        $this->calendarMonth = now()->month;
        $this->calendarYear = now()->year;
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:leave-approvals,LeaveRequestSubmitted' => 'refreshData',
            'echo-private:leave-approvals,LeaveRequestApproved'  => 'refreshData',
            'echo-private:leave-approvals,LeaveRequestRejected'  => 'refreshData',
            'echo-private:leave-approvals,LeaveRequestCancelled' => 'refreshData',
        ];
    }

    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function approveRequest(int $id, LeaveService $leaveService): void
    {
        $req = LeaveRequest::findOrFail($id);
        if (!Auth::user()->can('approve', $req)) {
            abort(403, 'Unauthorized to approve leave.');
        }

        try {
            $leaveService->approve($req, Auth::user());
            $this->successMessage = "Leave request #{$req->id} approved successfully.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function openRejectModal(int $id): void
    {
        $this->rejectRequestId = $id;
        $this->rejectRemarks = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
    }

    public function confirmReject(LeaveService $leaveService): void
    {
        $this->validate([
            'rejectRemarks' => 'required|string|max:1000',
        ]);

        $req = LeaveRequest::findOrFail($this->rejectRequestId);
        if (!Auth::user()->can('reject', $req)) {
            abort(403, 'Unauthorized to reject leave.');
        }

        try {
            $leaveService->reject($req, Auth::user(), $this->rejectRemarks);
            $this->successMessage = "Leave request #{$req->id} rejected.";
            $this->showRejectModal = false;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function prevMonth(): void
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
    }

    public function toggleTypeActive(int $typeId): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        $type = LeaveType::findOrFail($typeId);
        $type->is_active = !$type->is_active;
        $type->save();
        $this->successMessage = "Leave type {$type->leave_code} status updated.";
    }

    public function editType(int $typeId): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        $type = LeaveType::findOrFail($typeId);
        $this->editingTypeId = $type->id;
        $this->editMaxYear = $type->max_days_per_year ?? 0;
        $this->editMaxLifetime = $type->max_days_lifetime ?? 0;
        $this->editMinService = $type->min_service_years ?? 0;
    }

    public function saveType(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        $type = LeaveType::findOrFail($this->editingTypeId);
        $type->update([
            'max_days_per_year' => $this->editMaxYear > 0 ? $this->editMaxYear : null,
            'max_days_lifetime' => $this->editMaxLifetime > 0 ? $this->editMaxLifetime : null,
            'min_service_years' => $this->editMinService,
        ]);

        $this->editingTypeId = null;
        $this->successMessage = "Leave type {$type->leave_code} settings updated.";
    }

    public function exportReportCsv()
    {
        $query = LeaveRequest::with(['user', 'leaveType'])
            ->when($this->reportStatus, fn($q) => $q->where('status', $this->reportStatus))
            ->when($this->reportLeaveType, fn($q) => $q->where('leave_type_id', $this->reportLeaveType))
            ->when($this->reportStartDate, fn($q) => $q->where('start_date', '>=', $this->reportStartDate))
            ->when($this->reportEndDate, fn($q) => $q->where('end_date', '<=', $this->reportEndDate))
            ->get();

        $filename = "leave_report_" . now()->format('Y_m_d_His') . ".csv";

        return response()->streamDownload(function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Staff Name', 'Email', 'Role', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Status', 'Applied On']);
            foreach ($query as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user->name ?? 'N/A',
                    $row->user->email ?? 'N/A',
                    $row->user->role ?? 'N/A',
                    $row->leaveType->leave_name ?? 'N/A',
                    $row->start_date->format('Y-m-d'),
                    $row->end_date->format('Y-m-d'),
                    $row->total_days,
                    $row->status,
                    $row->applied_on->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $pendingRequests = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        // Calendar approved leaves for calendarMonth/calendarYear
        $startOfMonth = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $calendarApproved = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'Approved')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
            })->get();

        $leaveTypes = LeaveType::all();

        $reportsQuery = LeaveRequest::with(['user', 'leaveType'])
            ->when($this->reportStatus, fn($q) => $q->where('status', $this->reportStatus))
            ->when($this->reportLeaveType, fn($q) => $q->where('leave_type_id', $this->reportLeaveType))
            ->when($this->reportStartDate, fn($q) => $q->where('start_date', '>=', $this->reportStartDate))
            ->when($this->reportEndDate, fn($q) => $q->where('end_date', '<=', $this->reportEndDate))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.leave-admin-dashboard', [
            'pendingRequests'  => $pendingRequests,
            'calendarApproved' => $calendarApproved,
            'startOfMonth'     => $startOfMonth,
            'endOfMonth'       => $endOfMonth,
            'leaveTypes'       => $leaveTypes,
            'reportsQuery'     => $reportsQuery,
        ]);
    }
}
