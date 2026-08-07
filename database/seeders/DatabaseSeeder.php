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

        if (class_exists(\Database\Seeders\DummyDataSeeder::class)) {
            $this->call(\Database\Seeders\DummyDataSeeder::class);
        }

        if (class_exists(\Database\Seeders\ProjectSeeder::class)) {
            $this->call(\Database\Seeders\ProjectSeeder::class);
        }
    }
}
