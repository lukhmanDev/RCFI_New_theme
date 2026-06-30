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
        User::create([
            'name' => 'Super Admin',
            'email' => 'sdigibeat@gmail.com',
            'mobile' => 9999999999,
            'role' => 1,
            'password' => bcrypt('support@123'),
            'designation' => 'Super Admin',
        ]);

        $this->call(DummyDataSeeder::class);
        $this->call(ProjectSeeder::class);
    }
}
