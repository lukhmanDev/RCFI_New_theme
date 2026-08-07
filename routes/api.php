<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeaveApiController;

Route::middleware(['auth'])->group(function () {
    Route::get('/leave-types', [LeaveApiController::class, 'leaveTypes']);
    Route::get('/users/{user}/leave-balances', [LeaveApiController::class, 'userBalances']);
    Route::get('/users/{user}/leave-eligibility', [LeaveApiController::class, 'userEligibility']);
    Route::post('/leave-requests', [LeaveApiController::class, 'store']);
    Route::get('/leave-requests', [LeaveApiController::class, 'index']);
    Route::patch('/leave-requests/{leaveRequest}/approve', [LeaveApiController::class, 'approve']);
    Route::patch('/leave-requests/{leaveRequest}/reject', [LeaveApiController::class, 'reject']);
    Route::patch('/leave-requests/{leaveRequest}/cancel', [LeaveApiController::class, 'cancel']);
    Route::get('/admin/leave-report', [LeaveApiController::class, 'report']);
});
