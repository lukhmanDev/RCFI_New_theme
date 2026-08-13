<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('leave_code', 10)->unique();
            $table->string('leave_name', 50);
            $table->string('description')->nullable();
            $table->enum('accrual_type', ['Monthly', 'Annual', 'OneTime', 'None']);
            $table->decimal('max_days_per_year', 5, 1)->nullable();
            $table->decimal('max_days_lifetime', 5, 1)->nullable();
            $table->boolean('carry_forward')->default(false);
            $table->enum('applicable_gender', ['All', 'Male', 'Female'])->default('All');
            $table->enum('requires_marital_status', ['Any', 'Married', 'Single'])->default('Any');
            $table->unsignedSmallInteger('min_service_years')->default(0);
            $table->boolean('is_lifetime_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
