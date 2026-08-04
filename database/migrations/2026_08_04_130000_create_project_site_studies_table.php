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
        if (!Schema::hasTable('project_site_studies')) {
            Schema::create('project_site_studies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->string('project_type');
                $table->longText('report')->nullable(); // Stores 1000+ words report
                $table->text('remarks')->nullable();
                $table->string('file_path')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('ticked_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->index(['project_id', 'project_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_site_studies');
    }
};
