<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    private $categories = [
        'education-center' => [
            'name' => 'Education Center',
            'view' => 'applications.education_center',
            'model' => \App\Models\EducationCenterApplication::class
        ],
        'cultural-center' => [
            'name' => 'Cultural Center',
            'view' => 'applications.cultural_center',
            'model' => \App\Models\CulturalCenterApplication::class
        ],
        'hospital-or-clinics' => [
            'name' => 'Hospital or Clinics',
            'view' => 'applications.hospital_clinics',
            'model' => \App\Models\HospitalClinicApplication::class
        ],
        'shops-and-others' => [
            'name' => 'Shops and Others',
            'view' => 'applications.shops_others',
            'model' => \App\Models\ShopOtherApplication::class
        ],
        'house' => [
            'name' => 'House',
            'view' => 'applications.house',
            'model' => \App\Models\HouseApplication::class
        ],
        'drinking-water-group-level' => [
            'name' => 'Drinking Water - Group Level',
            'view' => 'applications.drinking_water_group',
            'model' => \App\Models\DrinkingWaterGroupApplication::class
        ],
        'drinking-water-individual-level' => [
            'name' => 'Drinking Water - Individual Level',
            'view' => 'applications.drinking_water_individual',
            'model' => \App\Models\DrinkingWaterIndividualApplication::class
        ],
        'orphan-care' => [
            'name' => 'Orphan Care',
            'view' => 'applications.orphan_care',
            'model' => \App\Models\OrphanCareApplication::class
        ],
        'differently-abled' => [
            'name' => 'Differently Abled',
            'view' => 'applications.differently_abled',
            'model' => \App\Models\DifferentlyAbledApplication::class
        ],
        'family-aid' => [
            'name' => 'Family Aid',
            'view' => 'applications.family_aid',
            'model' => \App\Models\FamilyAidApplication::class
        ],
        'general' => [
            'name' => 'General',
            'view' => 'applications.general',
            'model' => \App\Models\GeneralApplication::class
        ]
    ];

    public function index()
    {
        $counts = [];
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $counts[$config['name']] = $model::count();
        }

        return view('admin.applications', compact('counts'));
    }

    public function showCategory($slug)
    {
        if (!array_key_exists($slug, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$slug];
        $categoryName = $config['name'];
        $categorySlug = $slug;
        $model = $config['model'];

        // Retrieve only applications in this category
        $applications = $model::orderBy('created_at', 'desc')->get();

        return view($config['view'], compact('applications', 'categoryName', 'categorySlug'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'applicant_name' => ['required', 'string', 'min:2', 'max:255'],
            'category' => ['required', 'string'],
            'amount_requested' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Pending,Approved,Rejected'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'details' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ]);

        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config) {
            // Find category config by category name
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $data['category']) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $model::create($data);
            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application registered successfully!');
        }

        // Fallback if category is not mapped to a model
        abort(400, 'Invalid category');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'applicant_name' => ['required', 'string', 'min:2', 'max:255'],
            'category' => ['required', 'string'],
            'amount_requested' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Pending,Approved,Rejected'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'details' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ]);

        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $data['category']) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $application = $model::findOrFail($id);
            $application->update($data);
            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application details updated successfully!');
        }

        abort(400, 'Invalid category');
    }

    public function destroy(Request $request, $id)
    {
        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $request->input('category')) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $application = $model::findOrFail($id);
            $application->delete();
            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application record deleted successfully.');
        }

        abort(400, 'Invalid category');
    }

    public function export($category)
    {
        if (!array_key_exists($category, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$category];
        $model = $config['model'];
        $applications = $model::all();

        // 1. Gather all unique metadata keys from JSON column
        $metaKeys = [];
        foreach ($applications as $app) {
            $meta = $app->meta;
            if (is_array($meta)) {
                $metaKeys = array_merge($metaKeys, array_keys($meta));
            } elseif (is_string($meta)) {
                $decoded = json_decode($meta, true);
                if (is_array($decoded)) {
                    $metaKeys = array_merge($metaKeys, array_keys($decoded));
                }
            }
        }
        $metaKeys = array_unique($metaKeys);

        // 2. Prepare headers
        $headers = [
            'Application ID',
            'Applicant Name',
            'Amount Requested',
            'Status',
            'Contact Email',
            'Details',
            'Created At'
        ];
        
        foreach ($metaKeys as $key) {
            $headers[] = ucwords(str_replace('_', ' ', $key));
        }

        // 3. Output stream
        $prefixes = [
            'education-center' => 'EC',
            'cultural-center' => 'CC',
            'hospital-or-clinics' => 'HC',
            'shops-and-others' => 'SO',
            'house' => 'HS',
            'drinking-water-group-level' => 'DWG',
            'drinking-water-individual-level' => 'DWI',
            'orphan-care' => 'OC',
            'differently-abled' => 'DA',
            'family-aid' => 'FA',
            'general' => 'GN'
        ];
        $prefix = $prefixes[$category] ?? 'APP';

        $callback = function() use ($applications, $headers, $metaKeys, $prefix) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($applications as $appItem) {
                $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                $appId = 'APLRCFI' . $appYear . $prefix . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);

                $row = [
                    $appId,
                    $appItem->applicant_name,
                    $appItem->amount_requested,
                    $appItem->status,
                    $appItem->contact_email,
                    $appItem->details,
                    $appItem->created_at
                ];

                $meta = $appItem->meta;
                if (is_string($meta)) {
                    $meta = json_decode($meta, true);
                }
                foreach ($metaKeys as $key) {
                    $row[] = $meta[$key] ?? '';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        $filename = str_replace(' ', '_', strtolower($config['name'])) . '_applications_' . date('Ymd_His') . '.csv';

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
