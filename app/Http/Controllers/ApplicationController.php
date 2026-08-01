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
    private $groupedCategories = [
        'Construction Applications' => [
            'education-center' => [ 'name' => 'Education Center', 'slug' => 'education-center' ],
            'cultural-center' => [ 'name' => 'Cultural Center', 'slug' => 'cultural-center' ],
            'hospital-or-clinics' => [ 'name' => 'Hospital or Clinics', 'slug' => 'hospital-or-clinics' ],
            'shops-and-others' => [ 'name' => 'Shops and Others', 'slug' => 'shops-and-others' ],
            'house' => [ 'name' => 'House', 'slug' => 'house' ],
        ],
        'Drinking Water Applications' => [
            'drinking-water-group-level' => [ 'name' => 'Drinking Water - Group Level', 'slug' => 'drinking-water-group-level' ],
            'drinking-water-individual-level' => [ 'name' => 'Drinking Water - Individual Level', 'slug' => 'drinking-water-individual-level' ],
        ],
        'Social Aid & Care' => [
            'orphan-care' => [ 'name' => 'Orphan Care', 'slug' => 'orphan-care' ],
            'differently-abled' => [ 'name' => 'Differently Abled', 'slug' => 'differently-abled' ],
            'family-aid' => [ 'name' => 'Family Aid', 'slug' => 'family-aid' ],
        ],
        'General Schemes' => [
            'general' => [ 'name' => 'General', 'slug' => 'general' ],
        ]
    ];

    public function index()
    {
        $counts = [];
        $pendingCounts = [];
        $approvedProjectCounts = [];
        $totalProjectCounts = [];
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $counts[$config['name']] = $model::count();
            $pendingCounts[$config['name']] = $model::where('status', 'Pending')->count();
            $approvedProjectCounts[$config['name']] = $model::where('status', 'Approved')->count();
            $totalProjectCounts[$config['name']] = $model::count();
        }

        $categories = $this->categories;
        $groupedCategories = $this->groupedCategories;

        if (auth()->user() && auth()->user()->isSocialAid()) {
            $socialAidSlugs = ['orphan-care', 'differently-abled', 'family-aid'];
            $categories = array_filter($categories, fn($key) => in_array($key, $socialAidSlugs), ARRAY_FILTER_USE_KEY);
            $groupedCategories = array_filter($groupedCategories, fn($key) => $key === 'Social Aid & Care', ARRAY_FILTER_USE_KEY);
        }

        return view('admin.applications', compact('categories', 'groupedCategories', 'counts', 'pendingCounts', 'approvedProjectCounts', 'totalProjectCounts'));
    }

    public function showCategory($slug)
    {
        if (!array_key_exists($slug, $this->categories)) {
            abort(404);
        }

        if (auth()->user() && auth()->user()->isSocialAid() && !in_array($slug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            return redirect()->route('applications.category', 'orphan-care')->with('error', 'Social Aid Manager can only access Social Aid categories.');
        }

        $config = $this->categories[$slug];
        $categoryName = $config['name'];
        $categorySlug = $slug;
        $model = $config['model'];

        // Retrieve only applications in this category
        $user = auth()->user();
        $statusOrderRaw = "CASE 
            WHEN LOWER(status) = 'pending' THEN 1 
            WHEN LOWER(status) = 'approved' THEN 2 
            WHEN LOWER(status) = 'rejected' THEN 3 
            ELSE 4 
        END ASC";

        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $applications = $model::with(['address', 'cluster'])
                ->orderByRaw($statusOrderRaw)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $applications = $model::with('address')
                ->orderByRaw($statusOrderRaw)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $projectModel = str_replace('Application', 'Project', $model);
        $projectsMap = [];
        if (class_exists($projectModel)) {
            $appIds = $applications->pluck('id')->toArray();
            $query = $projectModel::whereIn('application_id', $appIds)
                ->with(['donor', 'projectManager']);
            $projects = $this->scopeProjectsForUser($query, $user)
                ->get()
                ->keyBy('application_id');
            $projectsMap = $projects->toArray();
        }

        $clusters = \App\Models\Cluster::orderBy('name', 'asc')->get();
        $donors = \App\Models\Donor::orderBy('name', 'asc')->get();

        return view($config['view'], compact('applications', 'categoryName', 'categorySlug', 'projectsMap', 'clusters', 'donors'));
    }

    public function store(Request $request)
    {
        $rules = [
            'applicant_name' => ['required', 'string', 'min:2', 'max:255'],
            'category' => ['required', 'string'],
            'amount_requested' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Pending,Approved,Rejected'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'additional_note' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
            'house_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'town' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'panchayat' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'pin_code' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'mobile_1' => ['nullable', 'string', 'max:255'],
            'mobile_2' => ['nullable', 'string', 'max:255'],
            'contact_number_1' => ['nullable', 'string', 'max:255'],
            'contact_number_2' => ['nullable', 'string', 'max:255'],
            'cluster_id' => ['nullable', 'exists:clusters,id'],
            'agency_number' => ['nullable', 'string', 'max:255'],
        ];

        if ($request->input('status') === 'Approved') {
            $rules['cluster_id'] = ['required', 'exists:clusters,id'];
            $rules['agency_number'] = ['required', 'string', 'max:255'];
        }

        if ($request->hasFile('student_photo')) {
            $path = $request->file('student_photo')->store('student_photos', 'public');
            $meta = $request->input('meta', []);
            $meta['student_photo'] = '/storage/' . $path;
            $request->merge(['meta' => $meta]);
        }

        $data = $request->validate($rules, [
            'cluster_id.required' => 'The cluster field is required when the application is approved.',
            'agency_number.required' => 'The agency number field is required when the application is approved.',
        ]);

        if (isset($meta['student_photo'])) {
            $data['meta'] = array_merge($data['meta'] ?? [], ['student_photo' => $meta['student_photo']]);
        }

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

            $addressFields = ['house_name', 'place', 'post_office', 'town', 'village', 'panchayat', 'district', 'state', 'pin_code', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];
            
            $rawAddress = [];
            foreach ($addressFields as $af) {
                if ($request->filled($af)) {
                    $rawAddress[$af] = $request->input($af);
                } elseif ($request->filled("meta.{$af}")) {
                    $rawAddress[$af] = $request->input("meta.{$af}");
                }
            }

            $dbAddressFields = ['house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code', 'contact_number_1', 'contact_number_2'];
            $addressData = array_intersect_key($rawAddress, array_flip($dbAddressFields));
            if (!isset($addressData['contact_number_1'])) {
                $mob = $rawAddress['mobile_1'] ?? ($rawAddress['mobile'] ?? null);
                if ($mob) { $addressData['contact_number_1'] = $mob; }
            }
            if (!isset($addressData['contact_number_2'])) {
                $mob2 = $rawAddress['mobile_2'] ?? null;
                if ($mob2) { $addressData['contact_number_2'] = $mob2; }
            }

            if (isset($data['details']) && !isset($data['additional_note'])) {
                $data['additional_note'] = $data['details'];
            }
            unset($data['details']);

            $categoryName = $data['category'] ?? ($config['name'] ?? '');
            unset($data['category']);
            foreach ($addressFields as $f) {
                unset($data[$f]);
            }

            $appItem = $model::create($data);
            if (!empty(array_filter($addressData))) {
                $appItem->address()->updateOrCreate([], array_filter($addressData));
            }

            try {
                \App\Models\Notification::create([
                    'title' => 'New Application',
                    'message' => 'A new application by "' . $data['applicant_name'] . '" has been registered under ' . $categoryName . '.',
                    'url' => route('applications.category', $redirectCategory)
                ]);
            } catch (\Exception $e) {
                \Log::error('Notification creation failed: ' . $e->getMessage());
            }
            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application registered successfully!');
        }

        // Fallback if category is not mapped to a model
        abort(400, 'Invalid category');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAdminAccess()) {
            return redirect()->back()->with('error', 'You are not authorized to edit applications.');
        }

        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config && $request->filled('category')) {
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $request->input('category')) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                $m = $c['model'];
                if ($m::where('id', $id)->exists()) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $application = $model::findOrFail($id);
            if (strtolower($application->status ?? 'pending') === 'approved') {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Approved applications cannot be edited by any role.'], 403);
                }
                return redirect()->back()->with('error', 'Approved applications cannot be edited by any role.');
            }
        }

        $rules = [
            'applicant_name' => ['required', 'string', 'min:2', 'max:255'],
            'category' => ['nullable', 'string'],
            'amount_requested' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:Pending,Approved,Rejected'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'additional_note' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
            'house_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'town' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'panchayat' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'pin_code' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'mobile_1' => ['nullable', 'string', 'max:255'],
            'mobile_2' => ['nullable', 'string', 'max:255'],
            'contact_number_1' => ['nullable', 'string', 'max:255'],
            'contact_number_2' => ['nullable', 'string', 'max:255'],
            'cluster_id' => ['nullable', 'exists:clusters,id'],
            'agency_number' => ['nullable', 'string', 'max:255'],
        ];

        $redirectCategory = $request->input('redirect_category');
        $isClusterRequired = in_array($request->input('category'), ['Orphan Care', 'Differently Abled', 'Family Aid']) 
            || in_array($redirectCategory, ['orphan-care', 'differently-abled', 'family-aid']);
        if ($isClusterRequired && $request->input('status') === 'Approved') {
            $rules['cluster_id'] = ['required', 'exists:clusters,id'];
            $rules['agency_number'] = ['required', 'string', 'max:255'];
        }

        if ($request->hasFile('student_photo')) {
            $path = $request->file('student_photo')->store('student_photos', 'public');
            $meta = $request->input('meta', []);
            $meta['student_photo'] = '/storage/' . $path;
            $request->merge(['meta' => $meta]);
        }

        $data = $request->validate($rules, [
            'cluster_id.required' => 'The cluster field is required when the application is approved.',
            'agency_number.required' => 'The agency number field is required when the application is approved.',
        ]);

        if (isset($meta['student_photo'])) {
            $data['meta'] = array_merge($data['meta'] ?? [], ['student_photo' => $meta['student_photo']]);
        }

        if (!$config) {
            $redirectCategory = $request->input('redirect_category');
            $config = $this->categories[$redirectCategory] ?? null;
            if (!$config) {
                foreach ($this->categories as $slug => $c) {
                    if ($c['name'] === ($data['category'] ?? '')) {
                        $config = $c;
                        $redirectCategory = $slug;
                        break;
                    }
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

            $addressFields = ['house_name', 'place', 'post_office', 'town', 'village', 'panchayat', 'district', 'state', 'pin_code', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];
            
            $rawAddress = [];
            foreach ($addressFields as $af) {
                if ($request->filled($af)) {
                    $rawAddress[$af] = $request->input($af);
                } elseif ($request->filled("meta.{$af}")) {
                    $rawAddress[$af] = $request->input("meta.{$af}");
                }
            }

            $dbAddressFields = ['house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code', 'contact_number_1', 'contact_number_2'];
            $addressData = array_intersect_key($rawAddress, array_flip($dbAddressFields));
            if (!isset($addressData['contact_number_1'])) {
                $mob = $rawAddress['mobile_1'] ?? ($rawAddress['mobile'] ?? null);
                if ($mob) { $addressData['contact_number_1'] = $mob; }
            }
            if (!isset($addressData['contact_number_2'])) {
                $mob2 = $rawAddress['mobile_2'] ?? null;
                if ($mob2) { $addressData['contact_number_2'] = $mob2; }
            }

            if (isset($data['details']) && !isset($data['additional_note'])) {
                $data['additional_note'] = $data['details'];
            }
            unset($data['details']);

            unset($data['category']);
            foreach ($addressFields as $f) {
                unset($data[$f]);
            }

            $application = $model::findOrFail($id);
            $application->update($data);
            if (!empty(array_filter($addressData))) {
                $application->address()->updateOrCreate([], array_filter($addressData));
            }

            return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application details updated successfully!');
        }

        abort(400, 'Invalid category');
    }

    public function show($id)
    {
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            if ($model::where('id', $id)->exists()) {
                return redirect()->route('applications.category', $slug);
            }
        }
        return redirect()->route('applications.index');
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->canDeleteApplications()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'You are not authorized to delete applications.'], 403);
            }
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

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                $m = $c['model'];
                if ($m::where('id', $id)->exists()) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $application = $model::findOrFail($id);
            if (strtolower($application->status ?? 'pending') === 'approved') {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Approved applications cannot be deleted by any role.'], 403);
                }
                return redirect()->back()->with('error', 'Approved applications cannot be deleted by any role.');
            }

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

            $projectModel = $projectModels[$redirectCategory] ?? null;
            if ($projectModel) {
                $projectModel::where('application_id', $application->id)->delete();
            }

            $application->delete();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Application record deleted successfully.']);
            }

            if ($redirectCategory) {
                return redirect()->route('applications.category', $redirectCategory)->with('success', 'Application record deleted successfully.');
            }

            return redirect()->back()->with('success', 'Application record deleted successfully.');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'error' => 'Invalid category'], 400);
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
        $metaKeys = array_values(array_diff(array_unique($metaKeys), ['category']));

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

    public function approveApplication(Request $request, $category, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->canApproveApplications()) {
            return redirect()->back()->with('error', 'You are not authorized to approve applications.');
        }

        $config = $this->categories[$category] ?? null;
        if (!$config) {
            abort(404, 'Category not found');
        }

        $model = $config['model'];
        $app = $model::findOrFail($id);

        if (in_array($category, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $request->validate([
                'cluster_id' => ['required', 'exists:clusters,id'],
                'agency_number' => ['required', 'string', 'max:255'],
            ], [
                'cluster_id.required' => 'The cluster field is required.',
                'agency_number.required' => 'The agency number field is required.',
            ]);

            $app->cluster_id = $request->input('cluster_id');
            $app->agency_number = $request->input('agency_number');
        }

        $app->status = 'Approved';
        $app->save();

        try {
            \App\Models\Notification::create([
                'title' => 'Application Approved',
                'message' => 'Application for "' . $app->applicant_name . '" has been approved.',
                'url' => route('applications.category', $category)
            ]);
        } catch (\Exception $e) {
            \Log::error('Notification creation failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Application approved successfully.');
    }

    public function rejectApplication(Request $request, $category, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->canApproveApplications()) {
            return redirect()->back()->with('error', 'You are not authorized to reject applications.');
        }

        $config = $this->categories[$category] ?? null;
        if (!$config) {
            abort(404, 'Category not found');
        }

        $model = $config['model'];
        $app = $model::findOrFail($id);

        if (($app->sponsor_status ?? 'Not Sponsored') === 'Sponsored') {
            return redirect()->back()->with('error', 'Sponsored applications cannot be rejected. Please remove sponsorship first.');
        }

        $app->status = 'Rejected';

        $request->validate([
            'remarks' => ['required', 'string', 'min:2', 'max:1000'],
        ], [
            'remarks.required' => 'A rejection reason is mandatory when rejecting an application.',
        ]);

        $remarks = $request->input('remarks');
        $app->rejected_reason = $remarks;
        $app->additional_note = ($app->additional_note ? $app->additional_note . "\n" : "") . "Rejection Reason: " . $remarks;

        $app->save();

        try {
            \App\Models\Notification::create([
                'title' => 'Application Rejected',
                'message' => 'Application for "' . $app->applicant_name . '" has been rejected.',
                'url' => route('applications.category', $category)
            ]);
        } catch (\Exception $e) {
            \Log::error('Notification creation failed: ' . $e->getMessage());
        }

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
        if (auth()->user() && auth()->user()->isReception()) {
            return redirect()->route('applications.index')->with('error', 'Unauthorized access.');
        }

        $approvedCounts = [];
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $approvedCounts[$config['name']] = $model::where('status', 'Approved')->count();
        }

        $categories = $this->categories;
        $groupedCategories = $this->groupedCategories;

        if (auth()->user() && auth()->user()->isSocialAid()) {
            $socialAidSlugs = ['orphan-care', 'differently-abled', 'family-aid'];
            $categories = array_filter($categories, fn($key) => in_array($key, $socialAidSlugs), ARRAY_FILTER_USE_KEY);
            $groupedCategories = array_filter($groupedCategories, fn($key) => $key === 'Social Aid & Care', ARRAY_FILTER_USE_KEY);
        }

        return view('approved_applications.index', compact('categories', 'groupedCategories', 'approvedCounts'));
    }

    public function showApprovedCategory(Request $request, $category)
    {
        if (auth()->user() && auth()->user()->isReception()) {
            return redirect()->route('applications.index')->with('error', 'Unauthorized access.');
        }

        if (!array_key_exists($category, $this->categories)) {
            abort(404);
        }

        if (auth()->user() && auth()->user()->isSocialAid() && !in_array($category, ['orphan-care', 'differently-abled', 'family-aid'])) {
            return redirect()->route('applications.approved.category', 'orphan-care')->with('error', 'Social Aid Manager can only access Social Aid categories.');
        }

        $config = $this->categories[$category];
        $categoryName = $config['name'];
        $categorySlug = $category;
        $model = $config['model'];

        $sponsorStatusParam = $request->input('sponsor_status', 'all');
        $clusterIdParam = $request->input('cluster_id', 'all');

        $relations = [];
        $dummyModel = new $model;
        if (method_exists($dummyModel, 'cluster')) {
            $relations[] = 'cluster';
        }
        if (method_exists($dummyModel, 'address')) {
            $relations[] = 'address';
        }

        $query = $model::query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        $query->where('status', 'Approved');

        if ($sponsorStatusParam !== 'all' && !empty($sponsorStatusParam)) {
            $statusVal = strtolower(trim($sponsorStatusParam));
            if ($statusVal === 'sponsored') {
                $query->whereRaw("LOWER(sponsor_status) = 'sponsored'");
            } elseif (in_array($statusVal, ['not sponsored', 'un-sponsored', 'unsponsored', 'notsponsored'])) {
                $query->where(function($q) {
                    $q->whereRaw("LOWER(sponsor_status) != 'sponsored'")
                      ->orWhereNull('sponsor_status');
                });
            }
        }

        if ($clusterIdParam !== 'all' && !empty($clusterIdParam)) {
            $query->where('cluster_id', $clusterIdParam);
        }

        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $applications = $query
                ->orderByRaw("CASE 
                    WHEN LOWER(sponsor_status) = 'not sponsored' THEN 1 
                    WHEN LOWER(sponsor_status) = 'sponsored' THEN 2 
                    ELSE 3 
                END ASC")
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $applications = $query->orderBy('created_at', 'desc')->get();
        }
        
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
            $user = auth()->user();
            $query = $projectModel::with(['donor', 'projectManager'])
                ->whereIn('application_id', $appIds);
            $projectsMap = $this->scopeProjectsForUser($query, $user)
                ->get()
                ->keyBy('application_id');

            // Priority sorting for non-social-aid approved applications:
            // 1. Not Started (No project assigned)
            // 2. In Progress / Not set / other status
            // 3. Completed (Bottom of list)
            if (!in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
                $applications = $applications->sortBy(function ($app) use ($projectsMap) {
                    $project = $projectsMap[$app->id] ?? null;
                    if (!$project) {
                        return 1;
                    }
                    if ($project->status === 'Completed') {
                        return 3;
                    }
                    return 2;
                })->values();
            }
        }

        $viewName = str_replace('applications.', 'approved_applications.', $config['view']);

        $clusters = [];
        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $clusters = \App\Models\Cluster::orderBy('name', 'asc')->get();
        }
        $donors = \App\Models\Donor::orderBy('name', 'asc')->get();

        return view($viewName, compact('applications', 'categoryName', 'categorySlug', 'projectsMap', 'clusters', 'donors'));
    }

    public function exportApproved(Request $request)
    {
        if (auth()->user() && auth()->user()->isReception()) {
            return redirect()->route('applications.index')->with('error', 'Unauthorized access.');
        }

        set_time_limit(300);

        $categoryParam = $request->input('category', 'all');
        $sponsorStatusParam = $request->input('sponsor_status', 'all');
        $clusterIdParam = $request->input('cluster_id', 'all');
        $searchParam = strtolower(trim($request->input('search', '')));

        $targetCategories = [];
        if ($categoryParam !== 'all' && isset($this->categories[$categoryParam])) {
            $targetCategories[$categoryParam] = $this->categories[$categoryParam];
        } else {
            $targetCategories = $this->categories;
            if (auth()->user() && auth()->user()->isSocialAid()) {
                $socialAidSlugs = ['orphan-care', 'differently-abled', 'family-aid'];
                $targetCategories = array_filter($targetCategories, fn($key) => in_array($key, $socialAidSlugs), ARRAY_FILTER_USE_KEY);
            }
        }

        $allApprovedApps = collect();

        foreach ($targetCategories as $catSlug => $config) {
            $model = $config['model'];
            $dummyModel = new $model;
            $relations = [];
            if (method_exists($dummyModel, 'cluster')) {
                $relations[] = 'cluster';
            }
            if (method_exists($dummyModel, 'address')) {
                $relations[] = 'address';
            }

            $query = $model::query();
            if (!empty($relations)) {
                $query->with($relations);
            }
            $query->where('status', 'Approved');

            if ($sponsorStatusParam !== 'all' && !empty($sponsorStatusParam)) {
                $statusVal = strtolower(trim($sponsorStatusParam));
                if ($statusVal === 'sponsored') {
                    $query->whereRaw("LOWER(sponsor_status) = 'sponsored'");
                } elseif (in_array($statusVal, ['not sponsored', 'un-sponsored', 'unsponsored', 'notsponsored'])) {
                    $query->where(function($q) {
                        $q->whereRaw("LOWER(sponsor_status) != 'sponsored'")
                          ->orWhereNull('sponsor_status');
                    });
                }
            }

            if ($clusterIdParam !== 'all' && !empty($clusterIdParam)) {
                $query->where('cluster_id', $clusterIdParam);
            }

            $items = $query->orderBy('created_at', 'desc')->get();

            foreach ($items as $app) {
                $app->category_slug = $catSlug;
                $app->category_name = $config['name'];
                $allApprovedApps->push($app);
            }
        }

        if ($searchParam !== '') {
            $allApprovedApps = $allApprovedApps->filter(function($app) use ($searchParam) {
                $metaStr = is_array($app->meta) ? implode(' ', array_filter($app->meta, 'is_scalar')) : '';
                $searchable = strtolower(implode(' ', array_filter([
                    $app->applicant_name,
                    $app->agency_number,
                    $app->place,
                    $app->district,
                    $app->state,
                    $app->contact_email,
                    $app->contact_number_1,
                    $metaStr
                ])));
                return str_contains($searchable, $searchParam);
            });
        }

        // 1. Fast gathering of metadata keys using array keys set
        $metaKeysSet = [];
        foreach ($allApprovedApps as $appItem) {
            $meta = $appItem->meta;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            if (is_array($meta)) {
                foreach (array_keys($meta) as $k) {
                    if ($k !== 'category') {
                        $metaKeysSet[$k] = true;
                    }
                }
            }
        }
        $metaKeys = array_keys($metaKeysSet);

        // 2. Prepare comprehensive column headers
        $headers = [
            'Application ID',
            'Registration No',
            'Category',
            'Applicant Name',
            'Amount Requested',
            'Status',
            'Sponsor Status',
            'Cluster Code',
            'Cluster Name',
            'Agency Number',
            'Contact Email',
            'Contact Number 1',
            'Contact Number 2',
            'House Name',
            'Place',
            'Post Office',
            'Town',
            'Village',
            'Panchayat',
            'District',
            'State',
            'Pin Code',
            'Details / Note',
            'Created At',
            'Updated At'
        ];

        foreach ($metaKeys as $key) {
            $headers[] = ucwords(str_replace('_', ' ', $key));
        }

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

        $callback = function() use ($allApprovedApps, $headers, $metaKeys, $prefixes) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel UTF-8 decoding
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);

            $rowCount = 0;
            foreach ($allApprovedApps as $appItem) {
                $prefix = $prefixes[$appItem->category_slug] ?? 'APP';
                $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                $appId = 'APLRCFI' . $appYear . $prefix . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);

                $regNo = $appItem->reg_number ?? ($appItem->meta['reg_number'] ?? 'N/A');
                $clusterCode = $appItem->cluster ? $appItem->cluster->code : 'N/A';
                $clusterName = $appItem->cluster ? $appItem->cluster->name : 'N/A';

                $addr = $appItem->address;
                $houseName = $addr->house_name ?? ($appItem->house_name ?? ($appItem->meta['house_name'] ?? ''));
                $place = $addr->place ?? ($appItem->place ?? ($appItem->meta['place'] ?? ''));
                $postOffice = $addr->post_office ?? ($appItem->post_office ?? ($appItem->meta['post_office'] ?? ''));
                $town = $addr->town ?? ($appItem->town ?? ($appItem->meta['town'] ?? ''));
                $village = $addr->village ?? ($appItem->village ?? ($appItem->meta['village'] ?? ''));
                $panchayat = $addr->panchayat ?? ($appItem->panchayat ?? ($appItem->meta['panchayat'] ?? ''));
                $district = $addr->district ?? ($appItem->district ?? ($appItem->meta['district'] ?? ''));
                $state = $addr->state ?? ($appItem->state ?? ($appItem->meta['state'] ?? ''));
                $pinCode = $addr->pin_code ?? ($appItem->pin_code ?? ($appItem->meta['pin_code'] ?? ''));
                $contact1 = $addr->contact_number_1 ?? ($appItem->contact_number_1 ?? ($appItem->mobile ?? ''));
                $contact2 = $addr->contact_number_2 ?? ($appItem->contact_number_2 ?? ($appItem->mobile_2 ?? ''));

                $row = [
                    $appId,
                    $regNo,
                    $appItem->category_name,
                    $appItem->applicant_name,
                    $appItem->amount_requested,
                    $appItem->status,
                    $appItem->sponsor_status ?? 'N/A',
                    $clusterCode,
                    $clusterName,
                    $appItem->agency_number ?? 'N/A',
                    $appItem->contact_email ?? 'N/A',
                    $contact1,
                    $contact2,
                    $houseName,
                    $place,
                    $postOffice,
                    $town,
                    $village,
                    $panchayat,
                    $district,
                    $state,
                    $pinCode,
                    $appItem->details ?? ($appItem->additional_note ?? ''),
                    $appItem->created_at,
                    $appItem->updated_at
                ];

                $meta = $appItem->meta;
                if (is_string($meta)) {
                    $meta = json_decode($meta, true);
                }
                foreach ($metaKeys as $key) {
                    $val = $meta[$key] ?? '';
                    $row[] = is_array($val) ? json_encode($val) : $val;
                }

                fputcsv($file, $row);

                $rowCount++;
                if ($rowCount % 50 === 0) {
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }

            fclose($file);
        };

        $catNameStr = ($categoryParam !== 'all' && isset($this->categories[$categoryParam]))
            ? str_replace(' ', '_', strtolower($this->categories[$categoryParam]['name']))
            : 'all_categories';
        $filename = 'approved_applications_' . $catNameStr . '_' . date('Ymd_His') . '.csv';

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    private function scopeProjectsForUser($query, $user)
    {
        if (!$user) {
            return $query;
        }

        $designationLower = strtolower($user->designation ?? '');
        $isPm = ($user->role == 3 || str_contains($designationLower, 'project manager') || $designationLower === 'project manager');
        $isEngineer = ($user->role == 6 || str_contains($designationLower, 'engineer') || $designationLower === 'engineer');
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin');
        $isCoo = ($user->role == 2 || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo'));
        $isHod = ($user->role == 4 || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod'));

        if ($isSuperAdmin || $isCoo || $isHod) {
            return $query;
        }

        $table = $query->getModel()->getTable();
        $hasPm = \Illuminate\Support\Facades\Schema::hasColumn($table, 'project_manager_id');
        $hasEng = \Illuminate\Support\Facades\Schema::hasColumn($table, 'engineer_id');

        if ($isPm && $isEngineer) {
            if ($hasPm && $hasEng) {
                return $query->where(function ($q) use ($user) {
                    $q->where('project_manager_id', $user->id)
                      ->orWhere('engineer_id', $user->id);
                });
            } elseif ($hasPm) {
                return $query->where('project_manager_id', $user->id);
            } elseif ($hasEng) {
                return $query->where('engineer_id', $user->id);
            }
            return $query->whereRaw('1 = 0');
        }

        if ($isPm) {
            return $hasPm ? $query->where('project_manager_id', $user->id) : $query->whereRaw('1 = 0');
        }

        if ($isEngineer) {
            return $hasEng ? $query->where('engineer_id', $user->id) : $query->whereRaw('1 = 0');
        }

        return $query;
    }

    private function findSocialAidApplication($id, $category = null)
    {
        $map = [
            'orphan-care' => \App\Models\OrphanCareApplication::class,
            'differently-abled' => \App\Models\DifferentlyAbledApplication::class,
            'family-aid' => \App\Models\FamilyAidApplication::class,
        ];

        if ($category && isset($map[$category])) {
            return $map[$category]::find($id);
        }

        foreach ($map as $model) {
            $app = $model::find($id);
            if ($app) {
                return $app;
            }
        }

        return null;
    }

    public function updateCluster(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Only Super Admin can edit cluster details after approval.'], 403);
            }
            return redirect()->back()->with('error', 'Only Super Admin can edit cluster details after approval.');
        }

        $request->validate([
            'cluster_id' => ['required', 'exists:clusters,id'],
            'agency_number' => ['required', 'string', 'max:255'],
        ], [
            'cluster_id.required' => 'The cluster field is required.',
            'agency_number.required' => 'The agency number field is required.',
        ]);

        $category = $request->input('category');
        $app = $this->findSocialAidApplication($id, $category);
        if (!$app) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Application not found.'], 404);
            }
            return redirect()->back()->with('error', 'Application not found.');
        }

        $app->cluster_id = $request->input('cluster_id');
        $app->agency_number = $request->input('agency_number');
        
        $meta = $app->meta ?? [];
        $metaInput = $request->input('meta', []);
        if (isset($metaInput['agency_name'])) {
            $meta['agency_name'] = $metaInput['agency_name'];
        }
        if (isset($metaInput['application_date'])) {
            $meta['application_date'] = $metaInput['application_date'];
        }
        $app->meta = $meta;
        $app->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cluster and Agency details updated successfully.',
                'cluster' => $app->cluster ? [
                    'id' => $app->cluster->id,
                    'name' => $app->cluster->name,
                    'code' => $app->cluster->code
                ] : null,
                'agency_number' => $app->agency_number,
                'meta' => $app->meta
            ]);
        }

        return redirect()->back()->with('success', 'Cluster and Agency Number updated successfully.');
    }

    public function toggleSponsor(Request $request, $id)
    {
        $isJson = $request->expectsJson() || $request->wantsJson() || $request->ajax();
        $user = auth()->user();
        if (!$user || !$user->canManageSponsorship()) {
            if ($isJson) {
                return response()->json(['success' => false, 'error' => 'You are not authorized to update sponsor status.'], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to update sponsor status.');
        }

        $category = $request->input('category');
        $app = $this->findSocialAidApplication($id, $category);
        if (!$app) {
            if ($isJson) {
                return response()->json(['success' => false, 'error' => 'Application not found.'], 404);
            }
            return redirect()->back()->with('error', 'Application not found.');
        }

        try {
            $meta = $app->meta ?? [];
            unset($meta['sponsor_status']);

            if ($app->sponsor_status === 'Sponsored' && !$request->has('sponsored_date')) {
                if (!$user->isSuperAdmin()) {
                    if ($isJson) {
                        return response()->json(['success' => false, 'error' => 'Only Super Admin can un-sponsor applications.'], 403);
                    }
                    return redirect()->back()->with('error', 'Only Super Admin can un-sponsor applications.');
                }

                $app->sponsor_status = 'Not Sponsored';
                unset($meta['sponsored_date']);
            } else {
                $app->sponsor_status = 'Sponsored';
                $meta['sponsored_date'] = $request->input('sponsored_date', date('Y-m-d'));
            }

            $app->meta = $meta;
            $app->save();

            if ($isJson) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sponsor status updated successfully.',
                    'sponsor_status' => $app->sponsor_status,
                    'sponsored_date' => $meta['sponsored_date'] ?? null
                ]);
            }

            return redirect()->back()->with('success', 'Sponsor status updated successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error toggling sponsor: ' . $e->getMessage());
            if ($isJson) {
                return response()->json(['success' => false, 'error' => 'Failed to update sponsor status: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to update sponsor status: ' . $e->getMessage());
        }
    }
}

