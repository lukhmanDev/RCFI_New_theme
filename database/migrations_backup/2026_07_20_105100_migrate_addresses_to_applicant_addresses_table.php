<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
                $houseName = $row->house_name ?? null;
                $place = $row->place ?? null;
                $postOffice = $row->post_office ?? ($row->post ?? null);
                $village = $row->village ?? null;
                $panchayat = $row->panchayat ?? ($row->panchayath ?? null);
                $district = $row->district ?? ($row->locality_district ?? null);
                $state = $row->state ?? ($row->locality_state ?? null);
                $pinCode = $row->pin_code ?? ($row->pin ?? ($row->pincode ?? null));
                $location = $row->location ?? ($row->locality_location ?? null);

                // Insert into applicant_addresses if any field is non-empty
                if ($houseName || $place || $postOffice || $village || $panchayat || $district || $state || $pinCode || $location) {
                    DB::table('applicant_addresses')->insert([
                        'addressable_type' => $modelClass,
                        'addressable_id' => $row->id,
                        'house_name' => $houseName,
                        'place' => $place,
                        'post_office' => $postOffice,
                        'village' => $village,
                        'panchayat' => $panchayat,
                        'district' => $district,
                        'state' => $state,
                        'pin_code' => $pinCode,
                        'location' => $location,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('applicant_addresses')->truncate();
    }
};
