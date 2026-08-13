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
        // 1. Change column definition to VARCHAR(50) with default 'super_admin'
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('super_admin')->change();
        });

        // 2. Map existing values to lowercase snake_case strings
        DB::table('users')->whereIn('role', ['1', 'Super Admin', 1])->update(['role' => 'super_admin']);
        DB::table('users')->whereIn('role', ['2', 'COO', 2])->update(['role' => 'coo']);
        DB::table('users')->whereIn('role', ['3', 'Project Manager', 3])->update(['role' => 'project_manager']);
        DB::table('users')->whereIn('role', ['4', 'HOD', 4])->update(['role' => 'hod']);
        DB::table('users')->whereIn('role', ['5', 'Others', 5])->update(['role' => 'others']);
        DB::table('users')->whereIn('role', ['6', 'Engineer', 6])->update(['role' => 'engineer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'super_admin')->update(['role' => '1']);
        DB::table('users')->where('role', 'coo')->update(['role' => '2']);
        DB::table('users')->where('role', 'project_manager')->update(['role' => '3']);
        DB::table('users')->where('role', 'hod')->update(['role' => '4']);
        DB::table('users')->where('role', 'others')->update(['role' => '5']);
        DB::table('users')->where('role', 'engineer')->update(['role' => '6']);

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('role')->default(1)->change();
        });
    }
};
