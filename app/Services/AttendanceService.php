<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceService
{
    public function clockIn(User $user, ?string $notes = null, ?string $ip = null, ?string $location = null): Attendance
    {
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = new Attendance([
                'user_id' => $user->id,
                'date' => $today,
            ]);
        }

        if ($attendance->clock_in) {
            // Already clocked in today
            return $attendance;
        }

        // Determine if late (e.g. after 09:30 AM)
        $lateThreshold = Carbon::createFromTimeString('09:30:00');
        $isLate = now()->gt($lateThreshold);
        $status = $attendance->status ?? ($isLate ? 'Late' : 'Present');

        $attendance->clock_in = $currentTime;
        $attendance->status = $status;
        if ($notes) $attendance->notes = $notes;
        if ($ip) $attendance->ip_address = $ip;
        if ($location) $attendance->location = $location;

        $attendance->save();

        return $attendance;
    }

    public function clockOut(User $user, ?string $notes = null): Attendance
    {
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = new Attendance([
                'user_id' => $user->id,
                'date' => $today,
                'status' => 'Present',
            ]);
        }

        $attendance->clock_out = $currentTime;
        if ($notes) {
            $attendance->notes = $attendance->notes ? ($attendance->notes . ' | ' . $notes) : $notes;
        }

        $attendance->save();

        return $attendance;
    }

    public function markAttendance(
        User $user,
        string $date,
        string $status,
        ?string $clockIn = null,
        ?string $clockOut = null,
        ?string $notes = null,
        ?User $markedBy = null
    ): Attendance {
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $date,
            ],
            [
                'status' => $status,
                'clock_in' => $clockIn ?: null,
                'clock_out' => $clockOut ?: null,
                'notes' => $notes,
                'marked_by' => $markedBy ? $markedBy->id : null,
            ]
        );

        return $attendance;
    }

    public function bulkMarkAttendance(array $records, string $date, User $markedBy): void
    {
        foreach ($records as $record) {
            if (empty($record['user_id'])) continue;

            $this->markAttendance(
                User::findOrFail($record['user_id']),
                $date,
                $record['status'] ?? 'Present',
                $record['clock_in'] ?? null,
                $record['clock_out'] ?? null,
                $record['notes'] ?? null,
                $markedBy
            );
        }
    }
}
