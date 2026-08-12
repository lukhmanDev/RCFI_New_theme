<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceWidget extends Component
{
    public string $notes = '';
    public string $successMessage = '';
    public string $errorMessage = '';

    public function clockIn(AttendanceService $attendanceService): void
    {
        try {
            $user = Auth::user();
            $ip = request()->ip();
            $attendanceService->clockIn($user, $this->notes ?: null, $ip);

            $this->successMessage = 'Successfully clocked in at ' . now()->format('h:i A');
            $this->reset('notes');
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function clockOut(AttendanceService $attendanceService): void
    {
        try {
            $user = Auth::user();
            $attendanceService->clockOut($user, $this->notes ?: null);

            $this->successMessage = 'Successfully clocked out at ' . now()->format('h:i A');
            $this->reset('notes');
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        $todayAttendance = $user ? Attendance::where('user_id', $user->id)->where('date', $today)->first() : null;

        $recentAttendance = $user ? Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get() : collect();

        // Calculate monthly attendance stats for user
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');

        $monthlyLogs = $user ? Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get() : collect();

        $presentDays = $monthlyLogs->whereIn('status', ['Present', 'Late'])->count();
        $halfDays = $monthlyLogs->where('status', 'HalfDay')->count();
        $absentDays = $monthlyLogs->where('status', 'Absent')->count();
        $leaveDays = $monthlyLogs->where('status', 'OnLeave')->count();

        return view('livewire.attendance-widget', [
            'todayAttendance'  => $todayAttendance,
            'recentAttendance' => $recentAttendance,
            'presentDays'      => $presentDays,
            'halfDays'         => $halfDays,
            'absentDays'       => $absentDays,
            'leaveDays'        => $leaveDays,
        ]);
    }
}
