<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Services\LeaveService;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Evaluate eligibility & balances for ALL active Leave Types
        $year = now()->year;
        $activeTypes = LeaveType::where('is_active', true)->get();
        $leaveTypesData = collect();

        foreach ($activeTypes as $type) {
            $isEligible = $user->isEligibleFor($type);
            $yearVal = $type->accrual_type === 'OneTime' ? null : $year;

            $balance = LeaveBalance::where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where(function($q) use ($yearVal) {
                    if ($yearVal) {
                        $q->where('year', $yearVal)->orWhereNull('year');
                    } else {
                        $q->whereNull('year');
                    }
                })
                ->first();

            if (!$balance && $isEligible) {
                $balance = LeaveBalance::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $type->id,
                    'year' => $yearVal,
                    'allocated_days' => $type->max_days_per_year ?? $type->max_days_lifetime ?? 0,
                    'used_days' => 0,
                    'carried_forward_days' => 0,
                ]);
            }

            $leaveTypesData->push((object)[
                'leaveType' => $type,
                'is_eligible' => $isEligible,
                'available_days' => $balance ? $balance->balance_days : $type->getAccruedEntitlementToDate($yearVal),
                'used_days' => $balance ? $balance->used_days : 0,
                'total_days' => $balance ? ($type->accrual_type === 'Monthly' ? $balance->accrued_days : ($balance->allocated_days + $balance->carried_forward_days)) : $type->getAccruedEntitlementToDate($yearVal),
                'reason_ineligible' => !$isEligible ? $type->getIneligibilityReason($user) : null,
            ]);
        }

        // 2. Fetch leave requests with user leave balances & leave requests eager loaded for hover popover
        $query = LeaveRequest::with(['user.leaveBalances.leaveType', 'user.leaveRequests', 'leaveType'])->orderBy('created_at', 'desc');

        if (!$user->hasAdminAccess()) {
            $query->where('user_id', $user->id);
        }

        $leaveRequests = $query->get();

        return view('admin.leave_requests', compact(
            'leaveRequests',
            'leaveTypesData'
        ));
    }

    public function store(Request $request, LeaveService $leaveService)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $leaveTypeId = $request->input('leave_type_id') ?: $request->input('leave_type');
        $startDate   = $request->input('start_date') ?: $request->input('from_date');
        $endDate     = $request->input('end_date') ?: $request->input('to_date');

        if (!$startDate || !$endDate) {
            return redirect()->back()->withErrors(['error' => 'Start date and end date are required.']);
        }

        // Match LeaveType model
        $leaveTypeModel = null;
        if (is_numeric($leaveTypeId)) {
            $leaveTypeModel = LeaveType::find($leaveTypeId);
        } else if (is_string($leaveTypeId)) {
            $leaveTypeModel = LeaveType::where('leave_code', $leaveTypeId)
                ->orWhere('leave_name', $leaveTypeId)
                ->first();
        }

        // Fallback if no specific type matched
        if (!$leaveTypeModel) {
            $leaveTypeModel = LeaveType::where('leave_code', 'CL')->first() ?: LeaveType::first();
        }

        try {
            $leaveService->submitRequest(
                auth()->user(),
                $leaveTypeModel,
                $startDate,
                $endDate,
                $request->reason
            );

            return redirect()->back()->with('success', "Leave request submitted successfully for approval!");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function approve($id, LeaveService $leaveService)
    {
        $leave = LeaveRequest::findOrFail($id);
        if (!auth()->user()->can('approve', $leave)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $leaveService->approve($leave, auth()->user());
            return redirect()->back()->with('success', "Leave request #{$leave->id} approved successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, $id, LeaveService $leaveService)
    {
        $leave = LeaveRequest::findOrFail($id);
        if (!auth()->user()->can('reject', $leave)) {
            abort(403, 'Unauthorized action.');
        }

        $remarks = $request->input('remarks', $request->input('rejection_reason', 'Rejected by manager'));

        try {
            $leaveService->reject($leave, auth()->user(), $remarks);
            return redirect()->back()->with('success', "Leave request #{$leave->id} rejected.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id, LeaveService $leaveService)
    {
        $leave = LeaveRequest::findOrFail($id);

        if (!auth()->user()->can('cancel', $leave) && !auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $leaveService->cancel($leave, auth()->user());
            return redirect()->back()->with('success', "Leave request record cancelled/deleted successfully.");
        } catch (\Exception $e) {
            $leave->delete();
            return redirect()->back()->with('success', "Leave request record deleted.");
        }
    }

    public function getStaffLeaveHistory($id)
    {
        $staff = \App\Models\User::with(['leaveRequests.leaveType', 'leaveBalances.leaveType'])->findOrFail($id);

        $history = $staff->leaveRequests->sortByDesc('created_at')->values()->map(function($lr) {
            return [
                'id' => $lr->id,
                'leave_type' => $lr->leaveType->name ?? $lr->leave_type ?? 'Leave',
                'leave_code' => $lr->leaveType->code ?? 'LV',
                'start_date' => $lr->start_date ? \Carbon\Carbon::parse($lr->start_date)->format('M d, Y') : '',
                'end_date' => $lr->end_date ? \Carbon\Carbon::parse($lr->end_date)->format('M d, Y') : '',
                'total_days' => $lr->total_days,
                'reason' => $lr->reason ?? 'N/A',
                'status' => $lr->status ?? 'Pending',
                'applied_on' => $lr->created_at ? $lr->created_at->format('M d, Y h:i A') : '',
                'remarks' => $lr->remarks ?? $lr->rejection_reason ?? '',
            ];
        });

        $balances = $staff->leaveBalances->map(function($lb) {
            return [
                'type_name' => $lb->leaveType->name ?? 'Leave',
                'type_code' => $lb->leaveType->code ?? 'LV',
                'allocated' => $lb->allocated_days + $lb->carried_forward_days,
                'used' => $lb->used_days,
                'available' => $lb->balance_days,
            ];
        });

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'designation' => $staff->designation ?? 'Staff Member',
                'photo' => $staff->profile && $staff->profile->photo ? asset($staff->profile->photo) : null,
                'initial' => strtoupper(substr($staff->name ?? 'U', 0, 1)),
                'total_taken' => $staff->leaveRequests->where('status', 'Approved')->sum('total_days'),
                'total_applications' => $staff->leaveRequests->count(),
            ],
            'balances' => $balances,
            'history' => $history,
        ]);
    }
}
