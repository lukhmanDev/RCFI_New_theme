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
        return view('admin.applications', compact('categories', 'groupedCategories', 'counts', 'pendingCounts', 'approvedProjectCounts', 'totalProjectCounts'));
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
        $user = auth()->user();
        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $applications = $model::with(['address', 'cluster'])->orderBy('created_at', 'desc')->get();
        } else {
            $applications = $model::with('address')->orderBy('created_at', 'desc')->get();
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

        $clusters = [];
        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $clusters = \App\Models\Cluster::orderBy('name', 'asc')->get();
        }

        return view($config['view'], compact('applications', 'categoryName', 'categorySlug', 'projectsMap', 'clusters'));
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

        $redirectCategory = $request->input('redirect_category');
        $isOrphanCare = ($request->input('category') === 'Orphan Care' || $redirectCategory === 'orphan-care');
        if ($isOrphanCare && $request->input('status') === 'Approved') {
            $rules['cluster_id'] = ['required', 'exists:clusters,id'];
            $rules['agency_number'] = ['required', 'string', 'max:255'];
        }

        $data = $request->validate($rules, [
            'cluster_id.required' => 'The cluster field is required when the application is approved.',
            'agency_number.required' => 'The agency number field is required when the application is approved.',
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

        $data = $request->validate($rules, [
            'cluster_id.required' => 'The cluster field is required when the application is approved.',
            'agency_number.required' => 'The agency number field is required when the application is approved.',
        ]);

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
        if (!$user || !$user->hasAdminAccess()) {
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
            if (!$user->isSuperAdmin() && strtolower($application->status ?? 'pending') !== 'pending') {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Only pending applications can be deleted.'], 403);
                }
                return redirect()->back()->with('error', 'Only pending applications can be deleted.');
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
        $app->status = 'Rejected';

        $remarks = $request->input('remarks');
        if ($remarks) {
            $app->rejected_reason = $remarks;
            $app->additional_note = ($app->additional_note ? $app->additional_note . "\n" : "") . "Rejection Reason: " . $remarks;
        }

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
        return view('approved_applications.index', compact('categories', 'groupedCategories', 'approvedCounts'));
    }

    public function showApprovedCategory($category)
    {
        if (auth()->user() && auth()->user()->isReception()) {
            return redirect()->route('applications.index')->with('error', 'Unauthorized access.');
        }

        if (!array_key_exists($category, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$category];
        $categoryName = $config['name'];
        $categorySlug = $category;
        $model = $config['model'];

        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $applications = $model::with('cluster')->where('status', 'Approved')->orderBy('created_at', 'desc')->get();
        } else {
            $applications = $model::where('status', 'Approved')->orderBy('created_at', 'desc')->get();
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
        }

        $viewName = str_replace('applications.', 'approved_applications.', $config['view']);

        $clusters = [];
        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $clusters = \App\Models\Cluster::orderBy('name', 'asc')->get();
        }

        return view($viewName, compact('applications', 'categoryName', 'categorySlug', 'projectsMap', 'clusters'));
    }

    private function scopeProjectsForUser($query, $user)
    {
        if (!$user) {
            return $query;
        }

        $designationLower = strtolower($user->designation ?? '');
        $isPm = ($user->role == 3 || str_contains($designationLower, 'project manager') || $designationLower === 'project manager');
        $isEngineer = ($user->role == 6 || str_contains($designationLower, 'engineer') || $designationLower === 'engineer');
        $isSuperAdmin = ($user->role == 1);
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
        if (!$user || !$user->hasAdminAccess()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized action.'], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to edit applications.');
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
        $app->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cluster and Agency Number updated successfully.',
                'cluster' => $app->cluster ? [
                    'id' => $app->cluster->id,
                    'name' => $app->cluster->name,
                    'code' => $app->cluster->code
                ] : null,
                'agency_number' => $app->agency_number
            ]);
        }

        return redirect()->back()->with('success', 'Cluster and Agency Number updated successfully.');
    }

    public function toggleSponsor(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAdminAccess()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'You are not authorized to update sponsor status.'], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to update sponsor status.');
        }

        $category = $request->input('category');
        $app = $this->findSocialAidApplication($id, $category);
        if (!$app) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Application not found.'], 404);
            }
            return redirect()->back()->with('error', 'Application not found.');
        }

        $app->sponsor_status = ($app->sponsor_status === 'Sponsored') ? 'Not Sponsored' : 'Sponsored';
        $app->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sponsor status updated successfully.',
                'sponsor_status' => $app->sponsor_status
            ]);
        }

        return redirect()->back()->with('success', 'Sponsor status updated successfully.');
    }
}

