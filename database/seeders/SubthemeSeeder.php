<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubthemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subthemes = array (
  0 => 
  array (
    'id' => 1,
    'theme_id' => 1,
    'name' => 'Dream Home - Construction of Single Room',
    'status' => 1,
  ),
  1 => 
  array (
    'id' => 2,
    'theme_id' => 1,
    'name' => 'Dream Home - Financial aid for constructing /repairing house',
    'status' => 1,
  ),
  2 => 
  array (
    'id' => 3,
    'theme_id' => 1,
    'name' => 'Dream Home - House for poor family',
    'status' => 1,
  ),
  3 => 
  array (
    'id' => 4,
    'theme_id' => 1,
    'name' => 'Dream Home - House repair for natural calamities',
    'status' => 1,
  ),
  4 => 
  array (
    'id' => 5,
    'theme_id' => 1,
    'name' => 'Dream Home - Support for Household Equipment',
    'status' => 1,
  ),
  5 => 
  array (
    'id' => 6,
    'theme_id' => 1,
    'name' => 'Dream Home - Tent',
    'status' => 1,
  ),
  6 => 
  array (
    'id' => 7,
    'theme_id' => 1,
    'name' => 'Dress distribution',
    'status' => 1,
  ),
  7 => 
  array (
    'id' => 8,
    'theme_id' => 1,
    'name' => 'Financial aid for poor families',
    'status' => 1,
  ),
  8 => 
  array (
    'id' => 9,
    'theme_id' => 1,
    'name' => 'Marriage aid',
    'status' => 1,
  ),
  9 => 
  array (
    'id' => 10,
    'theme_id' => 2,
    'name' => 'Bicycle distribution',
    'status' => 1,
  ),
  10 => 
  array (
    'id' => 11,
    'theme_id' => 2,
    'name' => 'Books / Reading Materials / Library Materials',
    'status' => 1,
  ),
  11 => 
  array (
    'id' => 12,
    'theme_id' => 2,
    'name' => 'Computer Center',
    'status' => 1,
  ),
  12 => 
  array (
    'id' => 13,
    'theme_id' => 2,
    'name' => 'Computer distribution',
    'status' => 1,
  ),
  13 => 
  array (
    'id' => 14,
    'theme_id' => 2,
    'name' => 'Construction of Academic Institution',
    'status' => 1,
  ),
  14 => 
  array (
    'id' => 15,
    'theme_id' => 2,
    'name' => 'Construction of Class Rooms / Schools - Eight classrooms',
    'status' => 1,
  ),
  15 => 
  array (
    'id' => 16,
    'theme_id' => 2,
    'name' => 'Construction of Class Rooms / Schools - Four classrooms',
    'status' => 1,
  ),
  16 => 
  array (
    'id' => 17,
    'theme_id' => 2,
    'name' => 'Construction of Class Rooms / Schools - More than Eight class rooms',
    'status' => 1,
  ),
  17 => 
  array (
    'id' => 18,
    'theme_id' => 2,
    'name' => 'Construction of Class Rooms / Schools - Single Classroom',
    'status' => 1,
  ),
  18 => 
  array (
    'id' => 19,
    'theme_id' => 2,
    'name' => 'Construction of Class Rooms / Schools - Two classrooms',
    'status' => 1,
  ),
  19 => 
  array (
    'id' => 20,
    'theme_id' => 2,
    'name' => 'Construction of Class Rooms / Schools -Three Classrooms',
    'status' => 1,
  ),
  20 => 
  array (
    'id' => 21,
    'theme_id' => 2,
    'name' => 'Financial Support for Academic Institutions',
    'status' => 1,
  ),
  21 => 
  array (
    'id' => 22,
    'theme_id' => 2,
    'name' => 'Hostel for Orphan Students  - 20 Students',
    'status' => 1,
  ),
  22 => 
  array (
    'id' => 23,
    'theme_id' => 2,
    'name' => 'Hostel for Orphan Students  - 24 Students',
    'status' => 1,
  ),
  23 => 
  array (
    'id' => 24,
    'theme_id' => 2,
    'name' => 'Hostel for Orphan Students  - 28 Students',
    'status' => 1,
  ),
  24 => 
  array (
    'id' => 25,
    'theme_id' => 2,
    'name' => 'Hostel for Orphan Students  - 48 Students',
    'status' => 1,
  ),
  25 => 
  array (
    'id' => 26,
    'theme_id' => 2,
    'name' => 'Infrastructural support for incompleted projects (No of classes)',
    'status' => 1,
  ),
  26 => 
  array (
    'id' => 27,
    'theme_id' => 2,
    'name' => 'Infrastructural Support for Schools and Collages',
    'status' => 1,
  ),
  27 => 
  array (
    'id' => 28,
    'theme_id' => 2,
    'name' => 'Scholarship for Higher Education',
    'status' => 1,
  ),
  28 => 
  array (
    'id' => 29,
    'theme_id' => 2,
    'name' => 'Scholarship for Primary or Secondary Education',
    'status' => 1,
  ),
  29 => 
  array (
    'id' => 30,
    'theme_id' => 2,
    'name' => 'Soft skill training',
    'status' => 1,
  ),
  30 => 
  array (
    'id' => 31,
    'theme_id' => 2,
    'name' => 'Stipend for Teachers Working in Remote Area',
    'status' => 1,
  ),
  31 => 
  array (
    'id' => 32,
    'theme_id' => 2,
    'name' => 'Support for other basic aminities',
    'status' => 1,
  ),
  32 => 
  array (
    'id' => 33,
    'theme_id' => 2,
    'name' => 'Teaching Learning Material Distribution',
    'status' => 1,
  ),
  33 => 
  array (
    'id' => 34,
    'theme_id' => 2,
    'name' => 'Undertaking of Aided Public School',
    'status' => 1,
  ),
  34 => 
  array (
    'id' => 35,
    'theme_id' => 2,
    'name' => 'Uniform',
    'status' => 1,
  ),
  35 => 
  array (
    'id' => 36,
    'theme_id' => 2,
    'name' => 'Widow care programme',
    'status' => 1,
  ),
  36 => 
  array (
    'id' => 37,
    'theme_id' => 3,
    'name' => 'Amenities for Personal Hygiene',
    'status' => 1,
  ),
  37 => 
  array (
    'id' => 38,
    'theme_id' => 3,
    'name' => 'Community drinking water scheme',
    'status' => 1,
  ),
  38 => 
  array (
    'id' => 39,
    'theme_id' => 3,
    'name' => 'Community Toilet',
    'status' => 1,
  ),
  39 => 
  array (
    'id' => 40,
    'theme_id' => 3,
    'name' => 'Community Toilets  - Along with CC',
    'status' => 1,
  ),
  40 => 
  array (
    'id' => 41,
    'theme_id' => 3,
    'name' => 'Personal Hygiene Corner - Along with CC',
    'status' => 1,
  ),
  41 => 
  array (
    'id' => 42,
    'theme_id' => 3,
    'name' => 'Personal Hygiene Corner - Separate',
    'status' => 1,
  ),
  42 => 
  array (
    'id' => 43,
    'theme_id' => 3,
    'name' => 'Water lifting device',
    'status' => 1,
  ),
  43 => 
  array (
    'id' => 44,
    'theme_id' => 3,
    'name' => 'Water Purifier / Public Drinking Amenities',
    'status' => 1,
  ),
  44 => 
  array (
    'id' => 45,
    'theme_id' => 3,
    'name' => 'Well - Bore well with Pump Set',
    'status' => 1,
  ),
  45 => 
  array (
    'id' => 46,
    'theme_id' => 3,
    'name' => 'Well - Hand Mark 1 or 2',
    'status' => 1,
  ),
  46 => 
  array (
    'id' => 47,
    'theme_id' => 3,
    'name' => 'Well - Hand Pump',
    'status' => 1,
  ),
  47 => 
  array (
    'id' => 48,
    'theme_id' => 3,
    'name' => 'Well - Open Well',
    'status' => 1,
  ),
  48 => 
  array (
    'id' => 49,
    'theme_id' => 3,
    'name' => 'Well - Open Well with Pump set',
    'status' => 1,
  ),
  49 => 
  array (
    'id' => 50,
    'theme_id' => 4,
    'name' => 'Food distribution -Iftar - Individual Level',
    'status' => 1,
  ),
  50 => 
  array (
    'id' => 51,
    'theme_id' => 4,
    'name' => 'Food kit distribution - Family Level',
    'status' => 1,
  ),
  51 => 
  array (
    'id' => 52,
    'theme_id' => 4,
    'name' => 'Food kit distribution - Uzhiyya - Family Level',
    'status' => 1,
  ),
  52 => 
  array (
    'id' => 53,
    'theme_id' => 5,
    'name' => 'Amenities',
    'status' => 1,
  ),
  53 => 
  array (
    'id' => 54,
    'theme_id' => 5,
    'name' => 'Cultural Center - 100 People',
    'status' => 1,
  ),
  54 => 
  array (
    'id' => 55,
    'theme_id' => 5,
    'name' => 'Cultural Center - 120 People',
    'status' => 1,
  ),
  55 => 
  array (
    'id' => 56,
    'theme_id' => 5,
    'name' => 'Cultural Center - 150 People',
    'status' => 1,
  ),
  56 => 
  array (
    'id' => 57,
    'theme_id' => 5,
    'name' => 'Cultural Center - 151 to 200 People',
    'status' => 1,
  ),
  57 => 
  array (
    'id' => 58,
    'theme_id' => 5,
    'name' => 'Cultural Center - 201 to 250 People',
    'status' => 1,
  ),
  58 => 
  array (
    'id' => 59,
    'theme_id' => 5,
    'name' => 'Cultural Center - 251 to 300 People',
    'status' => 1,
  ),
  59 => 
  array (
    'id' => 60,
    'theme_id' => 5,
    'name' => 'Cultural Center - 301 to 500 People',
    'status' => 1,
  ),
  60 => 
  array (
    'id' => 61,
    'theme_id' => 5,
    'name' => 'Cultural Center - For less than 100 people - Mostly for 80 people',
    'status' => 1,
  ),
  61 => 
  array (
    'id' => 62,
    'theme_id' => 5,
    'name' => 'Cultural Center - For more than 500 people',
    'status' => 1,
  ),
  62 => 
  array (
    'id' => 63,
    'theme_id' => 6,
    'name' => 'Blanket distribution & Winter Dress',
    'status' => 1,
  ),
  63 => 
  array (
    'id' => 64,
    'theme_id' => 6,
    'name' => 'Covid 19 Response',
    'status' => 1,
  ),
  64 => 
  array (
    'id' => 65,
    'theme_id' => 6,
    'name' => 'Food kit and medical kit distribution',
    'status' => 1,
  ),
  65 => 
  array (
    'id' => 66,
    'theme_id' => 7,
    'name' => 'Community Health Centers',
    'status' => 1,
  ),
  66 => 
  array (
    'id' => 67,
    'theme_id' => 7,
    'name' => 'Financial Aid - Dialysis',
    'status' => 1,
  ),
  67 => 
  array (
    'id' => 68,
    'theme_id' => 7,
    'name' => 'Financial Aid - Eye Surgery',
    'status' => 1,
  ),
  68 => 
  array (
    'id' => 69,
    'theme_id' => 7,
    'name' => 'Financial Aid - General Medical',
    'status' => 1,
  ),
  69 => 
  array (
    'id' => 70,
    'theme_id' => 7,
    'name' => 'Financial Aid - Heart Surgery',
    'status' => 1,
  ),
  70 => 
  array (
    'id' => 71,
    'theme_id' => 7,
    'name' => 'Financial Aid - Institutional Delivery',
    'status' => 1,
  ),
  71 => 
  array (
    'id' => 72,
    'theme_id' => 7,
    'name' => 'Hearing aid',
    'status' => 1,
  ),
  72 => 
  array (
    'id' => 73,
    'theme_id' => 7,
    'name' => 'Hospital',
    'status' => 1,
  ),
  73 => 
  array (
    'id' => 74,
    'theme_id' => 7,
    'name' => 'Medical camp',
    'status' => 1,
  ),
  74 => 
  array (
    'id' => 75,
    'theme_id' => 7,
    'name' => 'Medical Equipment - Air Bed',
    'status' => 1,
  ),
  75 => 
  array (
    'id' => 76,
    'theme_id' => 7,
    'name' => 'Medical Equipment - Gloco Meter',
    'status' => 1,
  ),
  76 => 
  array (
    'id' => 77,
    'theme_id' => 7,
    'name' => 'Medical Equipment - oxygen concentrator',
    'status' => 1,
  ),
  77 => 
  array (
    'id' => 78,
    'theme_id' => 7,
    'name' => 'Medical Equipment - Pressure Monitor',
    'status' => 1,
  ),
  78 => 
  array (
    'id' => 79,
    'theme_id' => 7,
    'name' => 'Medical Equipment - Walker',
    'status' => 1,
  ),
  79 => 
  array (
    'id' => 80,
    'theme_id' => 7,
    'name' => 'Medical Equipment - Wheel chair',
    'status' => 1,
  ),
  80 => 
  array (
    'id' => 81,
    'theme_id' => 7,
    'name' => 'Spectacles Distributions',
    'status' => 1,
  ),
  81 => 
  array (
    'id' => 82,
    'theme_id' => 8,
    'name' => 'Agriculture - Group Irrigation Well and Water Lifting Device',
    'status' => 1,
  ),
  82 => 
  array (
    'id' => 83,
    'theme_id' => 8,
    'name' => 'Agriculture - Plough and ox',
    'status' => 1,
  ),
  83 => 
  array (
    'id' => 84,
    'theme_id' => 8,
    'name' => 'Group IGA- Boat for small fisherman group',
    'status' => 1,
  ),
  84 => 
  array (
    'id' => 85,
    'theme_id' => 8,
    'name' => 'Group IGA- Tailoring Training Center for Women',
    'status' => 1,
  ),
  85 => 
  array (
    'id' => 86,
    'theme_id' => 8,
    'name' => 'Individual IGA - Animal Husbandry',
    'status' => 1,
  ),
  86 => 
  array (
    'id' => 87,
    'theme_id' => 8,
    'name' => 'Individual IGA - Cycle Rickshaw',
    'status' => 1,
  ),
  87 => 
  array (
    'id' => 88,
    'theme_id' => 8,
    'name' => 'Individual IGA - Mixer and Juicer',
    'status' => 1,
  ),
  88 => 
  array (
    'id' => 89,
    'theme_id' => 8,
    'name' => 'Individual IGA - Saw',
    'status' => 1,
  ),
  89 => 
  array (
    'id' => 90,
    'theme_id' => 8,
    'name' => 'Individual IGA - Shop',
    'status' => 1,
  ),
  90 => 
  array (
    'id' => 91,
    'theme_id' => 8,
    'name' => 'Individual IGA - Tailoring Machine',
    'status' => 1,
  ),
  91 => 
  array (
    'id' => 92,
    'theme_id' => 9,
    'name' => 'Financial aid for health and education expenses',
    'status' => 1,
  ),
  92 => 
  array (
    'id' => 93,
    'theme_id' => 9,
    'name' => 'Financial Support for Special Care Schools',
    'status' => 1,
  ),
  93 => 
  array (
    'id' => 94,
    'theme_id' => 9,
    'name' => 'Scooter For Physically Disabled',
    'status' => 1,
  ),
  94 => 
  array (
    'id' => 95,
    'theme_id' => 10,
    'name' => 'Solar Electrification Support',
    'status' => 1,
  ),
  95 => 
  array (
    'id' => 96,
    'theme_id' => 11,
    'name' => 'Admin',
    'status' => 1,
  ),
  96 => 
  array (
    'id' => 97,
    'theme_id' => 11,
    'name' => 'Human Resource',
    'status' => 1,
  ),
  97 => 
  array (
    'id' => 98,
    'theme_id' => 11,
    'name' => 'Capacity Building',
    'status' => 1,
  ),
);

        foreach ($subthemes as $subtheme) {
            DB::table('subthemes')->updateOrInsert(
                ['id' => $subtheme['id']],
                [
                    'theme_id' => $subtheme['theme_id'],
                    'name' => $subtheme['name'],
                    'status' => $subtheme['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
