<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_documents')) {
            Schema::table('project_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('project_documents', 'tax_receipt')) {
                    $table->string('tax_receipt')->default('0')->nullable();
                }
                if (!Schema::hasColumn('project_documents', 'tax_receipt_ticked_at')) {
                    $table->timestamp('tax_receipt_ticked_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('project_documents')) {
            Schema::table('project_documents', function (Blueprint $table) {
                if (Schema::hasColumn('project_documents', 'tax_receipt')) {
                    $table->dropColumn('tax_receipt');
                }
                if (Schema::hasColumn('project_documents', 'tax_receipt_ticked_at')) {
                    $table->dropColumn('tax_receipt_ticked_at');
                }
            });
        }
    }
};
