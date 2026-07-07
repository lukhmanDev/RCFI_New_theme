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
        $pendingCounts = [];
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $counts[$config['name']] = $model::count();
            $pendingCounts[$config['name']] = $model::where('status', 'Pending')->count();
        }

        return view('admin.applications', compact('counts', 'pendingCounts'));
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

        $projectsMap = [];
        $projectModels = [
            'education-center' => \App\Models\EducationCenterProject::class,
            'cultural-center' => \App\Models\CulturalCenterProject::class,
            'hospital-or-clinics' => \App\Models\HospitalClinicProject::class,
            'shops-and-others' => \App\Models\ShopOtherProject::class,
            'house' => \App\Models\HouseProject::class,
            'drinking-water-group-level' => \App\Models\DrinkingWaterGroupProject::class,
            'drinking-water-individual-level' => \App\Models\DrinkingWaterIndividualProject::class,
            'general' => \App\Models\GeneralProject::class,
        ];

        if (array_key_exists($slug, $projectModels)) {
            $projectModel = $projectModels[$slug];
            $appIds = $applications->pluck('id')->toArray();
            $projects = $projectModel::whereIn('application_id', $appIds)
                ->with(['donor', 'projectManager'])
                ->get()
                ->keyBy('application_id');
            $projectsMap = $projects->toArray();
        }

        return view($config['view'], compact('applications', 'categoryName', 'categorySlug', 'projectsMap'));
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
            'house_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'panchayat' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'pin_code' => ['nullable', 'string', 'max:255'],
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
            
            // Check if reg_number is unique
            if ($request->filled('meta.reg_number')) {
                $regNumber = $request->input('meta.reg_number');
                if ($model::where('reg_number', $regNumber)->exists()) {
                    return back()->withInput()->withErrors([
                        'meta.reg_number' => 'The registration number has already been taken.'
                    ]);
                }
            }

            $model::create($data);
            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application registered successfully!');
        }

        // Fallback if category is not mapped to a model
        abort(400, 'Invalid category');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, [1, 2, 4])) {
            return redirect()->back()->with('error', 'You are not authorized to edit applications.');
        }

        $data = $request->validate([
            'applicant_name' => ['required', 'string', 'min:2', 'max:255'],
            'category' => ['required', 'string'],
            'amount_requested' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Pending,Approved,Rejected'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'details' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
            'house_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'panchayat' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'pin_code' => ['nullable', 'string', 'max:255'],
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
            
            // Check if reg_number is unique excluding current record ID
            if ($request->filled('meta.reg_number')) {
                $regNumber = $request->input('meta.reg_number');
                if ($model::where('reg_number', $regNumber)->where('id', '!=', $id)->exists()) {
                    return back()->withInput()->withErrors([
                        'meta.reg_number' => 'The registration number has already been taken.'
                    ]);
                }
            }

            $application = $model::findOrFail($id);
            $application->update($data);
            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application details updated successfully!');
        }

        abort(400, 'Invalid category');
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        if (!in_array($user->role, [1, 2, 4])) {
            return redirect()->back()->with('error', 'You are not authorized to delete applications.');
        }

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

    public function showAll()
    {
        $allApplications = collect();
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $apps = $model::all();
            foreach ($apps as $app) {
                $app->category_slug = $slug;
                $app->category_name = $config['name'];
                $allApplications->push($app);
            }
        }

        $allApplications = $allApplications->sortByDesc('created_at');
        $categories = $this->categories;

        return view('admin.all_applications', compact('allApplications', 'categories'));
    }

    public function approveApplication($category, $id)
    {
        $user = auth()->user();
        if ($user->role !== 2) {
            return redirect()->back()->with('error', 'You are not authorized to approve applications.');
        }

        $config = $this->categories[$category] ?? null;
        if (!$config) {
            abort(404, 'Category not found');
        }

        $model = $config['model'];
        $app = $model::findOrFail($id);
        $app->status = 'Approved';
        $app->save();

        return redirect()->back()->with('success', 'Application approved successfully.');
    }

    public function rejectApplication($category, $id)
    {
        $user = auth()->user();
        if ($user->role !== 2) {
            return redirect()->back()->with('error', 'You are not authorized to reject applications.');
        }

        $config = $this->categories[$category] ?? null;
        if (!$config) {
            abort(404, 'Category not found');
        }

        $model = $config['model'];
        $app = $model::findOrFail($id);
        $app->status = 'Rejected';
        $app->save();

        // Delete project if it exists
        $projectModels = [
            'education-center' => \App\Models\EducationCenterProject::class,
            'cultural-center' => \App\Models\CulturalCenterProject::class,
            'hospital-or-clinics' => \App\Models\HospitalClinicProject::class,
            'shops-and-others' => \App\Models\ShopOtherProject::class,
            'house' => \App\Models\HouseProject::class,
            'drinking-water-group-level' => \App\Models\DrinkingWaterGroupProject::class,
            'drinking-water-individual-level' => \App\Models\DrinkingWaterIndividualProject::class,
            'orphan-care' => \App\Models\OrphanCareProject::class,
            'differently-abled' => \App\Models\DifferentlyAbledProject::class,
            'family-aid' => \App\Models\FamilyAidProject::class,
            'general' => \App\Models\GeneralProject::class,
        ];

        $projectModel = $projectModels[$category] ?? null;
        if ($projectModel) {
            $projectModel::where('application_id', $app->id)->delete();
        }

        return redirect()->back()->with('success', 'Application rejected successfully.');
    }

    public function showApprovedDashboard()
    {
        $approvedCounts = [];
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $approvedCounts[$config['name']] = $model::where('status', 'Approved')->count();
        }

        return view('approved_applications.index', compact('approvedCounts'));
    }

    public function showApprovedCategory($category)
    {
        if (!array_key_exists($category, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$category];
        $categoryName = $config['name'];
        $categorySlug = $category;
        $model = $config['model'];

        $applications = $model::where('status', 'Approved')->orderBy('created_at', 'desc')->get();
        
        // Find assigned projects
        $projectsMap = [];
        $projectModels = [
            'education-center' => \App\Models\EducationCenterProject::class,
            'cultural-center' => \App\Models\CulturalCenterProject::class,
            'hospital-or-clinics' => \App\Models\HospitalClinicProject::class,
            'shops-and-others' => \App\Models\ShopOtherProject::class,
            'house' => \App\Models\HouseProject::class,
            'drinking-water-group-level' => \App\Models\DrinkingWaterGroupProject::class,
            'drinking-water-individual-level' => \App\Models\DrinkingWaterIndividualProject::class,
            'orphan-care' => \App\Models\OrphanCareProject::class,
            'differently-abled' => \App\Models\DifferentlyAbledProject::class,
            'family-aid' => \App\Models\FamilyAidProject::class,
            'general' => \App\Models\GeneralProject::class,
        ];

        $projectModel = $projectModels[$category] ?? null;
        if ($projectModel) {
            $appIds = $applications->pluck('id')->toArray();
            $projectsMap = $projectModel::with(['donor', 'projectManager'])
                ->whereIn('application_id', $appIds)
                ->get()
                ->keyBy('application_id');
        }

        $viewName = str_replace('applications.', 'approved_applications.', $config['view']);

        return view($viewName, compact('applications', 'categoryName', 'categorySlug', 'projectsMap'));
    }
}
