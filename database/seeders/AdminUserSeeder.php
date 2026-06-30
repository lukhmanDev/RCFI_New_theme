<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
         User::firstOrCreate(
            ['email' => 
'sdigibeat@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('support@123'),
                'role' => 1,
            ]
        );
        //
    }
}
