<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create contractors table
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // 2. Add contractor_id to project_contractors
        Schema::table('project_contractors', function (Blueprint $table) {
            $table->unsignedBigInteger('contractor_id')->nullable()->after('project_type');
        });

        // 3. Migrate existing data and deduplicate
        $existing = DB::table('project_contractors')->get();
        $contractorMap = []; // key: name|phone|company, value: new contractor_id

        foreach ($existing as $row) {
            $name = trim($row->contractor_name);
            if (empty($name)) {
                continue;
            }

            $phone = trim($row->phone ?? '');
            $company = trim($row->company_name ?? '');
            $address = trim($row->address ?? '');

            $key = strtolower($name . '|' . $phone . '|' . $company);

            if (!isset($contractorMap[$key])) {
                $contractorId = DB::table('contractors')->insertGetId([
                    'name' => $name,
                    'phone' => !empty($phone) ? $phone : null,
                    'company_name' => !empty($company) ? $company : null,
                    'address' => !empty($address) ? $address : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $contractorMap[$key] = $contractorId;
            }

            DB::table('project_contractors')
                ->where('id', $row->id)
                ->update(['contractor_id' => $contractorMap[$key]]);
        }

        // 4. Add foreign key constraint to project_contractors
        Schema::table('project_contractors', function (Blueprint $table) {
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_contractors', function (Blueprint $table) {
            $table->dropForeign(['contractor_id']);
            $table->dropColumn('contractor_id');
        });

        Schema::dropIfExists('contractors');
    }
};
