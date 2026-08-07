<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('mobile');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->date('date_of_birth')->nullable()->after('mother_name');
            $table->date('date_of_joining')->nullable()->after('date_of_birth');
            $table->string('gender')->nullable()->after('date_of_joining');
            $table->string('marital_status')->nullable()->after('gender');
            $table->string('house_name')->nullable()->after('marital_status');
            $table->string('place')->nullable()->after('house_name');
            $table->string('po')->nullable()->after('place');
            $table->string('district')->nullable()->after('po');
            $table->string('state')->nullable()->after('district');
            $table->string('pin_code')->nullable()->after('state');
            $table->string('aadhar_number')->nullable()->after('pin_code');
            $table->string('pan_card_number')->nullable()->after('aadhar_number');
            $table->string('account_number')->nullable()->after('pan_card_number');
            $table->string('bank_name')->nullable()->after('account_number');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('bank_branch');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'father_name', 'mother_name', 'date_of_birth', 'date_of_joining',
                'gender', 'marital_status', 'house_name', 'place', 'po',
                'district', 'state', 'pin_code', 'aadhar_number', 'pan_card_number',
                'account_number', 'bank_name', 'bank_branch', 'ifsc_code',
            ]);
        });
    }
};
