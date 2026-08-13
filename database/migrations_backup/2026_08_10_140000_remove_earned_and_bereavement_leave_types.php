<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;

return new class extends Migration
{
    public function up(): void
    {
        $typeIds = LeaveType::whereIn('leave_code', ['EL', 'BL'])->pluck('id')->toArray();

        if (!empty($typeIds)) {
            LeaveBalance::whereIn('leave_type_id', $typeIds)->delete();
            LeaveRequest::whereIn('leave_type_id', $typeIds)->delete();
            LeaveType::whereIn('id', $typeIds)->delete();
        }
    }

    public function down(): void
    {
        // No roll back required for deleted temporary leave types
    }
};
