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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_hr')) {
                $table->boolean('is_hr')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'hod_id')) {
                $table->unsignedBigInteger('hod_id')->nullable()->after('role');
                $table->foreign('hod_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Sync existing assigned_hod_id to hod_id if assigned_hod_id column exists
        if (Schema::hasColumn('users', 'assigned_hod_id') && Schema::hasColumn('users', 'hod_id')) {
            DB::statement('UPDATE users SET hod_id = assigned_hod_id WHERE hod_id IS NULL AND assigned_hod_id IS NOT NULL');
            DB::statement('UPDATE users SET assigned_hod_id = hod_id WHERE assigned_hod_id IS NULL AND hod_id IS NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'hod_id')) {
                $table->dropForeign(['hod_id']);
                $table->dropColumn('hod_id');
            }
        });
    }
};
