<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Access to Adequate, Safe and Affordable Basic Services',
    'status' => 1,
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Access to Quality Education',
    'status' => 1,
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'Access to Safe Water, Sanitation and Hygiene',
    'status' => 1,
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'Access to Safe, Nutritious and Sufficient Food',
    'status' => 1,
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'Culture',
    'status' => 1,
  ),
  5 => 
  array (
    'id' => 6,
    'name' => 'Disaster & winter rehabilitation and support',
    'status' => 1,
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'Ensuring Healthy Lives and Promoting Well-being',
    'status' => 1,
  ),
  7 => 
  array (
    'id' => 8,
    'name' => 'Livelihood Development',
    'status' => 1,
  ),
  8 => 
  array (
    'id' => 9,
    'name' => 'Programme for Differently Abled',
    'status' => 1,
  ),
  9 => 
  array (
    'id' => 10,
    'name' => 'Renewable Energy',
    'status' => 1,
  ),
  10 => 
  array (
    'id' => 11,
    'name' => 'Institutional Expenditure',
    'status' => 1,
  ),
);

        foreach ($themes as $theme) {
            DB::table('themes')->updateOrInsert(
                ['id' => $theme['id']],
                [
                    'name' => $theme['name'],
                    'status' => $theme['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
