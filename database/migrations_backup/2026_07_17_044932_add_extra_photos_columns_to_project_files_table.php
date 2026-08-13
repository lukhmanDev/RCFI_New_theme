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
        Schema::table('project_files', function (Blueprint $table) {
            $table->text('photos_starting')->nullable()->after('photos_before');
            $table->text('photos_banner')->nullable()->after('photos_after');
            $table->text('photos_stone')->nullable()->after('photos_banner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_files', function (Blueprint $table) {
            $table->dropColumn(['photos_starting', 'photos_banner', 'photos_stone']);
        });
    }
};
