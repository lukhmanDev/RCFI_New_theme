<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE leave_types MODIFY COLUMN accrual_type ENUM('Monthly', 'Annual', 'OneTime', 'None') NOT NULL");
        }

        LeaveType::updateOrCreate(
            ['leave_code' => 'LWP'],
            [
                'leave_name' => 'Leave Without Pay',
                'description' => 'Unpaid leave without annual limit',
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
        LeaveType::where('leave_code', 'LWP')->delete();
    }
};
