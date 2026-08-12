<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceAdminDashboard extends Component
{
    public string $selectedDate = '';
    public string $searchStaff = '';
    public string $roleFilter = '';
    public string $activeTab = 'daily';

    // Daily marking state
    public array $attendanceData = [];

    // Monthly matrix state
    public int $matrixMonth;
    public int $matrixYear;

    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->matrixMonth = now()->month;
        $this->matrixYear = now()->year;

        $this->loadDailyAttendance();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadDailyAttendance();
    }

    public function updatedSearchStaff(): void
    {
        $this->loadDailyAttendance();
    }

    public function updatedRoleFilter(): void
    {
        $this->loadDailyAttendance();
    }

    public function loadDailyAttendance(): void
    {
        $currentUser = Auth::user();
        $users = User::nonSuperAdmin()
            ->where('id', '!=', $currentUser->id)
            ->forHod($currentUser)
            ->when($this->searchStaff, function ($q) {
                $q->where('name', 'like', '%' . $this->searchStaff . '%')
                  ->orWhere('email', 'like', '%' . $this->searchStaff . '%');
            })
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->orderBy('name', 'asc')
            ->get();

        $existing = Attendance::where('date', $this->selectedDate)
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->keyBy('user_id');

        $data = [];
        foreach ($users as $user) {
            $att = $existing->get($user->id);
            $data[$user->id] = [
                'status'    => $att->status ?? 'Present',
                'clock_in'  => $att && $att->clock_in ? Carbon::parse($att->clock_in)->format('H:i') : '',
                'clock_out' => $att && $att->clock_out ? Carbon::parse($att->clock_out)->format('H:i') : '',
                'notes'     => $att->notes ?? '',
            ];
        }

        $this->attendanceData = $data;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function markAllPresent(): void
    {
        foreach ($this->attendanceData as $userId => $item) {
            $this->attendanceData[$userId]['status'] = 'Present';
        }
        $this->successMessage = 'Marked all listed staff as Present in working form.';
    }

    public function saveDailyRecord(int $userId, AttendanceService $service): void
    {
        if (!isset($this->attendanceData[$userId])) return;

        $row = $this->attendanceData[$userId];
        $user = User::findOrFail($userId);

        try {
            $service->markAttendance(
                $user,
                $this->selectedDate,
                $row['status'],
                $row['clock_in'] ? ($row['clock_in'] . ':00') : null,
                $row['clock_out'] ? ($row['clock_out'] . ':00') : null,
                $row['notes'] ?: null,
                Auth::user()
            );

            $this->successMessage = "Attendance saved for {$user->name} on {$this->selectedDate}.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function saveAllDailyRecords(AttendanceService $service): void
    {
        try {
            $savedCount = 0;
            foreach ($this->attendanceData as $userId => $row) {
                $user = User::find($userId);
                if (!$user) continue;

                $service->markAttendance(
                    $user,
                    $this->selectedDate,
                    $row['status'],
                    !empty($row['clock_in']) ? ($row['clock_in'] . ':00') : null,
                    !empty($row['clock_out']) ? ($row['clock_out'] . ':00') : null,
                    !empty($row['notes']) ? $row['notes'] : null,
                    Auth::user()
                );
                $savedCount++;
            }

            $this->successMessage = "Saved attendance records for {$savedCount} staff members on {$this->selectedDate}.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function prevMatrixMonth(): void
    {
        $date = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->subMonth();
        $this->matrixMonth = $date->month;
        $this->matrixYear = $date->year;
    }

    public function nextMatrixMonth(): void
    {
        $date = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->addMonth();
        $this->matrixMonth = $date->month;
        $this->matrixYear = $date->year;
    }

    public function exportReportCsv()
    {
        $currentUser = Auth::user();
        $startOfMonth = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->endOfMonth()->format('Y-m-d');

        $records = Attendance::with('user')
            ->whereHas('user', fn($q) => $q->nonSuperAdmin()->forHod($currentUser))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get();

        $filename = "attendance_report_" . $this->matrixYear . "_" . sprintf('%02d', $this->matrixMonth) . ".csv";

        return response()->streamDownload(function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Staff Name', 'Email', 'Role', 'Date', 'Clock In', 'Clock Out', 'Working Hours', 'Status', 'Notes']);
            foreach ($records as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user->name ?? 'N/A',
                    $row->user->email ?? 'N/A',
                    $row->user->role ?? 'N/A',
                    $row->date ? $row->date->format('Y-m-d') : '',
                    $row->formatted_clock_in,
                    $row->formatted_clock_out,
                    $row->working_hours,
                    $row->status,
                    $row->notes ?? '',
                ]);
            }
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $currentUser = Auth::user();

        // Fetch all staff users for display (excl super admin, logged in user & forHod)
        $users = User::nonSuperAdmin()
            ->where('id', '!=', $currentUser->id)
            ->forHod($currentUser)
            ->when($this->searchStaff, function ($q) {
                $q->where('name', 'like', '%' . $this->searchStaff . '%')
                  ->orWhere('email', 'like', '%' . $this->searchStaff . '%');
            })
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->orderBy('name', 'asc')
            ->paginate(20);

        // Daily summary for selectedDate (excl super admin, logged in user & forHod)
        $dailySummaryLogs = Attendance::whereHas('user', fn($q) => $q->nonSuperAdmin()->where('id', '!=', $currentUser->id)->forHod($currentUser))
            ->where('date', $this->selectedDate)
            ->get();
        $totalStaff = User::nonSuperAdmin()->where('id', '!=', $currentUser->id)->forHod($currentUser)->count();
        $presentToday = $dailySummaryLogs->whereIn('status', ['Present', 'Late'])->count();
        $lateToday = $dailySummaryLogs->where('status', 'Late')->count();
        $absentToday = $dailySummaryLogs->where('status', 'Absent')->count();
        $onLeaveToday = $dailySummaryLogs->where('status', 'OnLeave')->count();

        // Monthly matrix data
        $startOfMonth = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        $monthAttendances = Attendance::whereHas('user', fn($q) => $q->nonSuperAdmin()->forHod($currentUser))
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        return view('livewire.attendance-admin-dashboard', [
            'users'            => $users,
            'totalStaff'       => $totalStaff,
            'presentToday'     => $presentToday,
            'lateToday'        => $lateToday,
            'absentToday'      => $absentToday,
            'onLeaveToday'     => $onLeaveToday,
            'daysInMonth'      => $daysInMonth,
            'monthAttendances' => $monthAttendances,
            'startOfMonth'     => $startOfMonth,
        ]);
    }
}
