<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::firstOrCreate(
            ['email' => 'sdigibeat@gmail.com'],
            [
                'name' => 'Super Admin',
                'mobile' => '9999999999',
                'role' => 1,
                'password' => Hash::make('support@123'),
                'designation' => 'Super Admin',
            ]
        );
    }
}
