<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = [
    \App\Models\EducationCenterApplication::class,
    \App\Models\CulturalCenterApplication::class,
    \App\Models\HospitalClinicApplication::class,
    \App\Models\ShopOtherApplication::class,
    \App\Models\HouseApplication::class,
    \App\Models\DrinkingWaterGroupApplication::class,
    \App\Models\DrinkingWaterIndividualApplication::class,
    \App\Models\OrphanCareApplication::class,
    \App\Models\DifferentlyAbledApplication::class,
    \App\Models\FamilyAidApplication::class,
    \App\Models\GeneralApplication::class,
];

$approved = 0; $pending = 0; $rejected = 0;
$statuses = [];
foreach ($models as $m) {
    $found = $m::select('status')->distinct()->pluck('status')->toArray();
    $statuses = array_unique(array_merge($statuses, $found));
}
echo "Distinct statuses: " . implode(', ', $statuses) . "\n";

echo "Approved: $approved, Pending: $pending, Rejected: $rejected\n";

$recent = [];
foreach ($models as $m) {
    $apps = $m::orderBy('created_at', 'desc')->take(5)->get();
    foreach ($apps as $a) {
        $recent[] = [
            'id' => $a->id,
            'applicant_name' => $a->applicant_name,
            'status' => $a->status,
            'created_at' => $a->created_at ? $a->created_at->toDateTimeString() : null,
            'model' => class_basename($m),
        ];
    }
}
usort($recent, function($a, $b) {
    return strcmp($b['created_at'], $a['created_at']);
});
print_r(array_slice($recent, 0, 5));
