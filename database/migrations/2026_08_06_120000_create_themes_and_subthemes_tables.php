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
        if (!Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subthemes')) {
            Schema::create('subthemes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
                $table->string('name');
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subthemes');
        Schema::dropIfExists('themes');
    }
};
