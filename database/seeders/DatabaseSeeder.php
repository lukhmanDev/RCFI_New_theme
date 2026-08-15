<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (class_exists(\Database\Seeders\AdminUserSeeder::class)) {
            $this->call(\Database\Seeders\AdminUserSeeder::class);
        } else {
            User::firstOrCreate(
                ['email' => 'sdigibeat@gmail.com'],
                [
                    'name' => 'Super Admin',
                    'mobile' => '9999999999',
                    'role' => 'super_admin',
                    'password' => bcrypt('support@123'),
                    'designation' => 'Super Admin',
                ]
            );
        }

        if (class_exists(\Database\Seeders\LeaveTypeSeeder::class)) {
            $this->call(\Database\Seeders\LeaveTypeSeeder::class);
        }

        if (class_exists(\Database\Seeders\ThemeSeeder::class)) {
            $this->call(\Database\Seeders\ThemeSeeder::class);
        }

        if (class_exists(\Database\Seeders\SubthemeSeeder::class)) {
            $this->call(\Database\Seeders\SubthemeSeeder::class);
        }

        if (class_exists(\Database\Seeders\PincodeMasterSeeder::class)) {
            $this->call(\Database\Seeders\PincodeMasterSeeder::class);
        }

        if (class_exists(\Database\Seeders\DummyDataSeeder::class)) {
            $this->call(\Database\Seeders\DummyDataSeeder::class);
        }

        if (class_exists(\Database\Seeders\ProjectSeeder::class)) {
            $this->call(\Database\Seeders\ProjectSeeder::class);
        }
    }
}
