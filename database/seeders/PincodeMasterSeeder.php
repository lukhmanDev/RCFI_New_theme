<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PincodeMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvGzPath = database_path('seeders/data/pincode_master.csv.gz');
        $csvPath = database_path('seeders/data/pincode_master.csv');

        // Check if data file exists
        if (!file_exists($csvGzPath) && !file_exists($csvPath)) {
            $this->command->warn("Pincode master data file not found at {$csvGzPath}");
            return;
        }

        // If table already has data, do not duplicate
        $existingCount = DB::table('pincode_master')->count();
        if ($existingCount > 0) {
            $this->command->info("Pincode master table already has {$existingCount} records. Skipping seed.");
            return;
        }

        // Open compressed or uncompressed CSV
        $gz = file_exists($csvGzPath) ? gzopen($csvGzPath, 'rb') : fopen($csvPath, 'r');
        if (!$gz) {
            $this->command->error("Unable to open pincode data file.");
            return;
        }

        // Read header
        $header = fgetcsv($gz);

        $batch = [];
        $batchSize = 2500;
        $totalInserted = 0;

        // Disable query log and temporarily disable foreign key / index checks if supported
        DB::disableQueryLog();
        DB::statement('SET foreign_key_checks = 0;');

        while (($row = fgetcsv($gz)) !== false) {
            if (empty($row) || count($row) < 10) {
                continue;
            }

            $batch[] = [
                'id'              => !empty($row[0]) ? (int)$row[0] : null,
                'circle_name'     => $row[1] ?? null,
                'region_name'     => $row[2] ?? null,
                'division_name'   => $row[3] ?? null,
                'office_name'     => $row[4] ?? null,
                'pincode'         => (int)($row[5] ?? 0),
                'office_type'     => $row[6] ?? null,
                'delivery_status' => $row[7] ?? null,
                'district'        => $row[8] ?? null,
                'state_name'      => $row[9] ?? null,
                'latitude'        => !empty($row[10]) ? (float)$row[10] : null,
                'longitude'       => !empty($row[11]) ? (float)$row[11] : null,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('pincode_master')->insert($batch);
                $totalInserted += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('pincode_master')->insert($batch);
            $totalInserted += count($batch);
        }

        gzclose($gz);
        DB::statement('SET foreign_key_checks = 1;');

        $this->command->info("Successfully seeded {$totalInserted} pincode master records.");
    }
}
