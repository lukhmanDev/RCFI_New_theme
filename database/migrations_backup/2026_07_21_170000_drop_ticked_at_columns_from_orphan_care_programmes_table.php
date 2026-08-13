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
        Schema::table('orphan_care_programmes', function (Blueprint $table) {
            $columnsToDrop = [
                'present_ticked_at',
                'photo_ticked_at',
                'marklist_ticked_at',
                'thanks_letter_ticked_at',
                'report_form_ticked_at',
                'other_document_ticked_at',
            ];

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('orphan_care_programmes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orphan_care_programmes', function (Blueprint $table) {
            $table->string('present_ticked_at')->nullable();
            $table->string('photo_ticked_at')->nullable();
            $table->string('marklist_ticked_at')->nullable();
            $table->string('thanks_letter_ticked_at')->nullable();
            $table->string('report_form_ticked_at')->nullable();
            $table->string('other_document_ticked_at')->nullable();
        });
    }
};
