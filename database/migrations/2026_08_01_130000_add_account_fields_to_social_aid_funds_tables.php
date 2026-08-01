<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['orphan_care_funds', 'family_aid_funds', 'differently_abled_funds'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'account_name')) {
                        $table->string('account_name')->nullable()->after('amount');
                    }
                    if (!Schema::hasColumn($tableName, 'account_number')) {
                        $table->string('account_number')->nullable()->after('account_name');
                    }
                    if (!Schema::hasColumn($tableName, 'ifsc_number')) {
                        $table->string('ifsc_number')->nullable()->after('account_number');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['orphan_care_funds', 'family_aid_funds', 'differently_abled_funds'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'ifsc_number')) {
                        $table->dropColumn('ifsc_number');
                    }
                    if (Schema::hasColumn($tableName, 'account_number')) {
                        $table->dropColumn('account_number');
                    }
                    if (Schema::hasColumn($tableName, 'account_name')) {
                        $table->dropColumn('account_name');
                    }
                });
            }
        }
    }
};
