<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Illuminate\Support\Facades\Auth;

class LeaveDashboard extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $leave_type_id = null;
    public string $start_date = '';
    public string $end_date = '';
    public bool $is_half_day = false;
    public string $half_day_session = 'First Half';
    public string $reason = '';
    public string $errorMessage = '';
    public string $successMessage = '';

    protected function getListeners(): array
    {
        $baseListeners = [
            'leave-updated' => 'refreshDashboard',
            'leaveRequestCreated' => 'refreshDashboard',
        ];

        $driver = config('broadcasting.default');
        if (!$driver || in_array($driver, ['null', 'log'])) {
            return $baseListeners;
        }

        $userId = Auth::id();
        return array_merge($baseListeners, [
            "echo-private:user.{$userId},LeaveRequestApproved" => 'refreshDashboard',
            "echo-private:user.{$userId},LeaveRequestRejected" => 'refreshDashboard',
            "echo-private:user.{$userId},LeaveRequestCancelled" => 'refreshDashboard',
            "echo-private:user.{$userId},LeaveBalanceUpdated" => 'refreshDashboard',
        ]);
    }

    public function refreshDashboard(): void
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->resetErrorBag();
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function submitLeave(LeaveService $leaveService): void
    {
        $this->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string|max:1000',
        ]);

        try {
            $user = Auth::user();
            $type = LeaveType::findOrFail($this->leave_type_id);

            $leaveService->submitRequest(
                $user,
                $type,
                $this->start_date,
                $this->end_date,
                $this->reason,
                $this->is_half_day,
                $this->half_day_session
            );

            $this->successMessage = 'Leave request submitted successfully!';
            $this->reset(['leave_type_id', 'start_date', 'end_date', 'is_half_day', 'half_day_session', 'reason', 'showModal']);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function cancelLeave(int $id, LeaveService $leaveService): void
    {
        $request = LeaveRequest::findOrFail($id);
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $leaveService->cancel($request);
            $this->successMessage = 'Leave request cancelled.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $leaveTypes = LeaveType::where('is_active', true)->get()->map(function ($type) use ($user) {
            $type->is_eligible = $user ? $user->isEligibleFor($type) : false;

            $year = $type->accrual_type === 'OneTime' ? null : now()->year;
            $balance = $user ? LeaveBalance::where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where(function ($q) use ($year) {
                    $q->where('year', $year)->orWhereNull('year');
                })->first() : null;

            $type->user_balance = $balance;
            return $type;
        });

        $myRequests = $user ? LeaveRequest::with(['leaveType', 'approver'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10) : collect();

        return view('livewire.leave-dashboard', [
            'leaveTypes'       => $leaveTypes,
            'myRequests'       => $myRequests,
            'showModal'        => $this->showModal,
            'leave_type_id'    => $this->leave_type_id,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'is_half_day'      => $this->is_half_day,
            'half_day_session' => $this->half_day_session,
            'reason'           => $this->reason,
            'successMessage'   => $this->successMessage,
            'errorMessage'     => $this->errorMessage,
        ]);
    }
}
