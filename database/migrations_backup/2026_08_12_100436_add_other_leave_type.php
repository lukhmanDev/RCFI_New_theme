<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LeaveType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        LeaveType::updateOrCreate(
            ['leave_code' => 'OL'],
            [
                'leave_name' => 'Other Leave',
                'description' => 'Special discretionary leave assigned exclusively by HR or COO',
                'accrual_type' => 'None',
                'max_days_per_year' => null,
                'max_days_lifetime' => null,
                'carry_forward' => false,
                'applicable_gender' => 'All',
                'requires_marital_status' => 'Any',
                'min_service_years' => 0,
                'is_lifetime_only' => false,
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        LeaveType::where('leave_code', 'OL')->delete();
    }
};
