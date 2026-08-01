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
        Schema::table('clusters', function (Blueprint $table) {
            if (!Schema::hasColumn('clusters', 'head_of_institution')) {
                $table->string('head_of_institution')->nullable()->after('institution_name');
            }
            if (!Schema::hasColumn('clusters', 'head_contact_number')) {
                $table->string('head_contact_number')->nullable()->after('head_of_institution');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clusters', function (Blueprint $table) {
            if (Schema::hasColumn('clusters', 'head_of_institution')) {
                $table->dropColumn('head_of_institution');
            }
            if (Schema::hasColumn('clusters', 'head_contact_number')) {
                $table->dropColumn('head_contact_number');
            }
        });
    }
};
