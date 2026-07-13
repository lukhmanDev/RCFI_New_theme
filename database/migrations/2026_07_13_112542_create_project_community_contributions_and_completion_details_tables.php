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
        Schema::create('project_community_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->string('item');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index(['project_id', 'project_type'], 'project_contrib_index');
        });

        Schema::create('project_completion_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_type');
            $table->decimal('total_project_cost', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('amount_paid_by_donor', 15, 2)->nullable();
            $table->decimal('community_contribution', 15, 2)->nullable();
            $table->decimal('any_other', 15, 2)->nullable();
            $table->decimal('deductions', 15, 2)->nullable();
            $table->date('handover_date')->nullable();
            $table->text('handover_remarks')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'project_type'], 'project_completion_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_completion_details');
        Schema::dropIfExists('project_community_contributions');
    }
};
