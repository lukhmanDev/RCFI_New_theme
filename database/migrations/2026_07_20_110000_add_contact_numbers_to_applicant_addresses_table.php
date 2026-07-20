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
        // 1. Add contact_number_1 and contact_number_2 to applicant_addresses
        if (Schema::hasTable('applicant_addresses')) {
            Schema::table('applicant_addresses', function (Blueprint $table) {
                if (!Schema::hasColumn('applicant_addresses', 'contact_number_1')) {
                    $table->string('contact_number_1')->nullable();
                }
                if (!Schema::hasColumn('applicant_addresses', 'contact_number_2')) {
                    $table->string('contact_number_2')->nullable();
                }
            });
        }

        // 2. Migrate existing contact data from application tables into applicant_addresses
        $tables = [
            'education_center_applications' => \App\Models\EducationCenterApplication::class,
            'cultural_center_applications' => \App\Models\CulturalCenterApplication::class,
            'hospital_clinic_applications' => \App\Models\HospitalClinicApplication::class,
            'shop_other_applications' => \App\Models\ShopOtherApplication::class,
            'house_applications' => \App\Models\HouseApplication::class,
            'drinking_water_group_applications' => \App\Models\DrinkingWaterGroupApplication::class,
            'drinking_water_individual_applications' => \App\Models\DrinkingWaterIndividualApplication::class,
            'orphan_care_applications' => \App\Models\OrphanCareApplication::class,
            'differently_abled_applications' => \App\Models\DifferentlyAbledApplication::class,
            'family_aid_applications' => \App\Models\FamilyAidApplication::class,
            'general_applications' => \App\Models\GeneralApplication::class,
        ];

        foreach ($tables as $tableName => $modelClass) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            $records = DB::table($tableName)->get();
            foreach ($records as $row) {
                $contact1 = $row->contact_number_1 ?? ($row->mobile_1 ?? ($row->mobile ?? null));
                $contact2 = $row->contact_number_2 ?? ($row->mobile_2 ?? null);

                if ($contact1 || $contact2) {
                    $existing = DB::table('applicant_addresses')
                        ->where('addressable_type', $modelClass)
                        ->where('addressable_id', $row->id)
                        ->first();

                    if ($existing) {
                        DB::table('applicant_addresses')
                            ->where('id', $existing->id)
                            ->update([
                                'contact_number_1' => $contact1,
                                'contact_number_2' => $contact2,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('applicant_addresses')->insert([
                            'addressable_type' => $modelClass,
                            'addressable_id' => $row->id,
                            'contact_number_1' => $contact1,
                            'contact_number_2' => $contact2,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // 3. Drop contact columns from application tables
        $contactColumns = ['contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];
        foreach (array_keys($tables) as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $contactColumns) {
                    foreach ($contactColumns as $col) {
                        if (Schema::hasColumn($tableName, $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applicant_addresses')) {
            Schema::table('applicant_addresses', function (Blueprint $table) {
                $table->dropColumn(['contact_number_1', 'contact_number_2']);
            });
        }
    }
};
