<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            if (!Schema::hasColumn('users', 'assigned_hod_id')) {
                $table->unsignedBigInteger('assigned_hod_id')->nullable()->after('is_hr');
                $table->foreign('assigned_hod_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'assigned_hod_id')) {
                $table->dropForeign(['assigned_hod_id']);
                $table->dropColumn('assigned_hod_id');
            }
            if (Schema::hasColumn('users', 'is_hr')) {
                $table->dropColumn('is_hr');
            }
        });
    }
};
