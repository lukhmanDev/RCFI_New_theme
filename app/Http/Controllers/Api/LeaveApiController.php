<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Requests\ApproveLeaveRequestRequest;
use App\Http\Requests\RejectLeaveRequestRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeaveApiController extends Controller
{
    protected LeaveService $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function leaveTypes(): JsonResponse
    {
        return response()->json(LeaveType::where('is_active', true)->get());
    }

    public function userBalances(User $user): JsonResponse
    {
        $year = now()->year;
        $balances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where(function ($q) use ($year) {
                $q->where('year', $year)->orWhereNull('year');
            })->get();

        return response()->json($balances);
    }

    public function userEligibility(User $user): JsonResponse
    {
        $types = LeaveType::where('is_active', true)->get()->map(function ($type) use ($user) {
            return [
                'leave_type' => $type,
                'is_eligible' => $user->isEligibleFor($type),
            ];
        });

        return response()->json($types);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = LeaveRequest::with(['user', 'leaveType', 'approver'])->orderBy('created_at', 'desc');

        if (!$user || !$user->hasAdminAccess()) {
            $query->where('user_id', $user?->id ?? 0);
        }

        return response()->json($query->paginate(20));
    }

    public function store(StoreLeaveRequestRequest $request): JsonResponse
    {
        $user = $request->user();
        $type = LeaveType::findOrFail($request->leave_type_id);

        $leaveRequest = $this->leaveService->submitRequest(
            $user,
            $type,
            $request->start_date,
            $request->end_date,
            $request->reason
        );

        return response()->json([
            'message' => 'Leave request submitted successfully.',
            'leave_request' => $leaveRequest,
        ], 201);
    }

    public function approve(ApproveLeaveRequestRequest $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $this->leaveService->approve($leaveRequest, $request->user());

        return response()->json([
            'message' => 'Leave request approved successfully.',
            'leave_request' => $leaveRequest->fresh(),
        ]);
    }

    public function reject(RejectLeaveRequestRequest $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $this->leaveService->reject($leaveRequest, $request->user(), $request->remarks);

        return response()->json([
            'message' => 'Leave request rejected.',
            'leave_request' => $leaveRequest->fresh(),
        ]);
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $user = $request->user();
        if ($leaveRequest->user_id !== $user->id || $leaveRequest->status !== 'Pending') {
            return response()->json(['message' => 'Unauthorized or request is not pending.'], 403);
        }

        $this->leaveService->cancel($leaveRequest);

        return response()->json([
            'message' => 'Leave request cancelled successfully.',
            'leave_request' => $leaveRequest->fresh(),
        ]);
    }

    public function report(Request $request): JsonResponse
    {
        if (!$request->user() || !$request->user()->hasAdminAccess()) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $report = LeaveRequest::with(['user', 'leaveType'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->leave_type_id, fn($q) => $q->where('leave_type_id', $request->leave_type_id))
            ->get();

        return response()->json($report);
    }
}
