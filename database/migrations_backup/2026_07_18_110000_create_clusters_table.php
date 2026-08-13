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
        Schema::create('clusters', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('institution_name')->nullable();
            $table->string('place')->nullable();
            $table->string('po')->nullable();
            $table->string('village')->nullable();
            $table->string('panjayath')->nullable();
            $table->string('dist')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('cordinator_name')->nullable();
            $table->string('cordinator_contact_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::table('orphan_care_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('cluster_id')->nullable()->after('status');
            $table->string('agency_number')->nullable()->after('cluster_id');

            $table->foreign('cluster_id')->references('id')->on('clusters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orphan_care_applications', function (Blueprint $table) {
            $table->dropForeign(['cluster_id']);
            $table->dropColumn(['cluster_id', 'agency_number']);
        });

        Schema::dropIfExists('clusters');
    }
};
