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
        Schema::table('project_statuses', function (Blueprint $table) {
            $table->timestamp('coo_approved_at')->nullable();
            $table->foreignId('coo_approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('coo_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_statuses', function (Blueprint $table) {
            $table->dropForeign(['coo_approver_id']);
            $table->dropColumn(['coo_approved_at', 'coo_approver_id', 'coo_remarks']);
        });
    }
};
