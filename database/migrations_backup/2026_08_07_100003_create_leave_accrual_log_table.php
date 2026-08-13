<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_accrual_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->unsignedTinyInteger('accrual_month')->nullable();
            $table->unsignedSmallInteger('accrual_year');
            $table->decimal('days_accrued', 5, 1);
            $table->timestamp('accrued_on')->useCurrent();
            $table->unique(['user_id', 'leave_type_id', 'accrual_year', 'accrual_month'], 'leave_accrual_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_accrual_log');
    }
};
