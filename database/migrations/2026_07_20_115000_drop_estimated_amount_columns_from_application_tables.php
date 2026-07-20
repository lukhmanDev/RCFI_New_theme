<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'education_center_applications',
            'cultural_center_applications',
            'hospital_clinic_applications',
            'shop_other_applications',
            'house_applications',
            'drinking_water_group_applications',
            'drinking_water_individual_applications',
            'orphan_care_applications',
            'differently_abled_applications',
            'family_aid_applications',
            'general_applications',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // Copy estimated_amount / expected_amount to amount_requested if amount_requested is null
            if (Schema::hasColumn($tableName, 'estimated_amount') || Schema::hasColumn($tableName, 'expected_amount')) {
                $records = DB::table($tableName)->get();
                foreach ($records as $row) {
                    $val = $row->estimated_amount ?? ($row->expected_amount ?? null);
                    if ($val && empty($row->amount_requested)) {
                        $numericVal = (int) preg_replace('/[^0-9]/', '', (string)$val);
                        if ($numericVal > 0) {
                            DB::table($tableName)->where('id', $row->id)->update(['amount_requested' => $numericVal]);
                        }
                    }
                }
            }

            // Drop redundant estimated_amount and expected_amount columns
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'estimated_amount')) {
                    $table->dropColumn('estimated_amount');
                }
                if (Schema::hasColumn($tableName, 'expected_amount')) {
                    $table->dropColumn('expected_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
