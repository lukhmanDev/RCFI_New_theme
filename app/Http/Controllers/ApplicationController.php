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
            $stats = $model::selectRaw("
                count(*) as total,
                sum(case when status = 'Pending' then 1 else 0 end) as pending,
                sum(case when status = 'Approved' then 1 else 0 end) as approved
            ")->first();
            $counts[$config['name']] = (int)($stats->total ?? 0);
            $pendingCounts[$config['name']] = (int)($stats->pending ?? 0);
            $approvedProjectCounts[$config['name']] = (int)($stats->approved ?? 0);
            $totalProjectCounts[$config['name']] = (int)($stats->total ?? 0);
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

        $relations = [];
        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid']) && method_exists($model, 'cluster')) {
            $relations[] = 'cluster';
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
            $relations[] = 'address';
        }

        $query = $model::query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        $applications = $query->orderByRaw($statusOrderRaw)
            ->orderBy('created_at', 'desc')
            ->get();

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
        if (auth()->user() && !auth()->user()->canAddApplications()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Users with role "Others" cannot add applications.'], 403);
            }
            return redirect()->back()->with('error', 'Users with role "Others" cannot add applications.');
        }

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

        $meta = $request->input('meta', []);
        if ($request->hasFile('student_photo')) {
            $path = $request->file('student_photo')->store('student_photos', 'public');
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
            
            // Check if reg_number is unique if column exists
            if ($request->filled('meta.reg_number')) {
                $regNumber = $request->input('meta.reg_number');
                $tableName = (new $model)->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'reg_number') && $model::where('reg_number', $regNumber)->exists()) {
                    return back()->withInput()->withErrors([
                        'meta.reg_number' => 'The registration number has already been taken.'
                    ]);
                }
            }

            $placeVal = $request->input('place') ?? ($request->input('location') ?? ($request->input('meta.place') ?? $request->input('meta.location')));
            $postVal = $request->input('post_office') ?? ($request->input('post') ?? ($request->input('meta.post_office') ?? $request->input('meta.post')));
            $panchayatVal = $request->input('panchayat') ?? ($request->input('panchayath') ?? ($request->input('meta.panchayat') ?? $request->input('meta.panchayath')));
            $pinVal = $request->input('pin_code') ?? ($request->input('pin') ?? ($request->input('meta.pin_code') ?? ($request->input('meta.pin') ?? $request->input('meta.locality_pin_code'))));
            $villageVal = $request->input('village') ?? $request->input('meta.village');
            $districtVal = $request->input('district') ?? $request->input('meta.district');
            $stateVal = $request->input('state') ?? $request->input('meta.state');
            $houseVal = $request->input('house_name') ?? $request->input('meta.house_name');
            $c1Val = $request->input('contact_number_1') ?? ($request->input('mobile_1') ?? ($request->input('mobile') ?? ($request->input('meta.contact_number_1') ?? ($request->input('meta.mobile_1') ?? $request->input('meta.mobile')))));
            $c2Val = $request->input('contact_number_2') ?? ($request->input('mobile_2') ?? ($request->input('meta.contact_number_2') ?? $request->input('meta.mobile_2')));

            $addressData = array_filter([
                'house_name' => $houseVal,
                'place' => $placeVal,
                'post_office' => $postVal,
                'village' => $villageVal,
                'panchayat' => $panchayatVal,
                'district' => $districtVal,
                'state' => $stateVal,
                'pin_code' => $pinVal,
                'contact_number_1' => $c1Val,
                'contact_number_2' => $c2Val,
            ]);
            $addressFields = ['house_name', 'place', 'location', 'post_office', 'post', 'town', 'village', 'panchayat', 'panchayath', 'district', 'state', 'pin_code', 'pin', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];

            if (isset($data['details']) && !isset($data['additional_note'])) {
                $data['additional_note'] = $data['details'];
            }
            unset($data['details']);

            $categoryName = $data['category'] ?? ($config['name'] ?? '');
            unset($data['category']);
            if (\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
                foreach ($addressFields as $f) {
                    unset($data[$f]);
                }
            } else {
                $data = array_merge($data, array_filter($addressData));
            }

            $tableName = (new $model)->getTable();
            $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing($tableName);
            foreach ($data as $k => $v) {
                if ($k !== 'meta' && !in_array($k, $tableColumns)) {
                    if ($v !== null && $v !== '') {
                        $data['meta'][$k] = $v;
                    }
                    unset($data[$k]);
                }
            }

            $appItem = $model::create($data);
            if (!empty(array_filter($addressData)) && \Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
                try {
                    $appItem->address()->updateOrCreate([], array_filter($addressData));
                } catch (\Throwable $e) {
                    \Log::warning('Applicant address creation warning: ' . $e->getMessage());
                }
            }

            try {
                broadcast(new \App\Events\ApplicationCreated($appItem))->toOthers();
            } catch (\Throwable $e) {
                \Log::warning('ApplicationCreated broadcast error: ' . $e->getMessage());
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

            $placeVal = $request->input('place') ?? ($request->input('location') ?? ($request->input('meta.place') ?? $request->input('meta.location')));
            $postVal = $request->input('post_office') ?? ($request->input('post') ?? ($request->input('meta.post_office') ?? $request->input('meta.post')));
            $panchayatVal = $request->input('panchayat') ?? ($request->input('panchayath') ?? ($request->input('meta.panchayat') ?? $request->input('meta.panchayath')));
            $pinVal = $request->input('pin_code') ?? ($request->input('pin') ?? ($request->input('meta.pin_code') ?? ($request->input('meta.pin') ?? $request->input('meta.locality_pin_code'))));
            $villageVal = $request->input('village') ?? $request->input('meta.village');
            $districtVal = $request->input('district') ?? $request->input('meta.district');
            $stateVal = $request->input('state') ?? $request->input('meta.state');
            $houseVal = $request->input('house_name') ?? $request->input('meta.house_name');
            $c1Val = $request->input('contact_number_1') ?? ($request->input('mobile_1') ?? ($request->input('mobile') ?? ($request->input('meta.contact_number_1') ?? ($request->input('meta.mobile_1') ?? $request->input('meta.mobile')))));
            $c2Val = $request->input('contact_number_2') ?? ($request->input('mobile_2') ?? ($request->input('meta.contact_number_2') ?? $request->input('meta.mobile_2')));

            $addressData = array_filter([
                'house_name' => $houseVal,
                'place' => $placeVal,
                'post_office' => $postVal,
                'village' => $villageVal,
                'panchayat' => $panchayatVal,
                'district' => $districtVal,
                'state' => $stateVal,
                'pin_code' => $pinVal,
                'contact_number_1' => $c1Val,
                'contact_number_2' => $c2Val,
            ]);
            $addressFields = ['house_name', 'place', 'location', 'post_office', 'post', 'town', 'village', 'panchayat', 'panchayath', 'district', 'state', 'pin_code', 'pin', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];

            if (isset($data['details']) && !isset($data['additional_note'])) {
                $data['additional_note'] = $data['details'];
            }
            unset($data['details']);

            unset($data['category']);
            if (\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
                foreach ($addressFields as $f) {
                    unset($data[$f]);
                }
            } else {
                $data = array_merge($data, array_filter($addressData));
            }

            $application = $model::findOrFail($id);
            $tableName = $application->getTable();
            $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing($tableName);
            foreach ($data as $k => $v) {
                if ($k !== 'meta' && !in_array($k, $tableColumns)) {
                    if ($v !== null && $v !== '') {
                        $data['meta'][$k] = $v;
                    }
                    unset($data[$k]);
                }
            }

            $application->update($data);
            if (!empty(array_filter($addressData)) && \Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
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
            if ($projectModel && $projectModel::where('application_id', $application->id)->exists()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Assigned applications connected to a project cannot be deleted.'], 403);
                }
                return redirect()->back()->with('error', 'Assigned applications connected to a project cannot be deleted.');
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
        if (auth()->user() && !auth()->user()->canDownloadExcel()) {
            return redirect()->back()->with('error', 'Users with role "Others" cannot download Excel exports.');
        }

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

        $rows = [];
        foreach ($applications as $appItem) {
            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
            $appId = 'APLRCFI' . $appYear . $prefix . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
            $createdAt = $appItem->created_at ? (is_string($appItem->created_at) ? date('Y-m-d H:i:s', strtotime($appItem->created_at)) : $appItem->created_at->format('Y-m-d H:i:s')) : '';

            $row = [
                $appId,
                $appItem->applicant_name,
                $appItem->amount_requested,
                $appItem->status,
                $appItem->contact_email,
                $appItem->details,
                $createdAt
            ];

            $meta = $appItem->meta;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            foreach ($metaKeys as $key) {
                $val = $meta[$key] ?? '';
                $row[] = $this->formatCsvCell($val, (string)$key);
            }

            $rows[] = $row;
        }

        $filename = str_replace(' ', '_', strtolower($config['name'])) . '_applications_' . date('Ymd_His') . '.xls';

        return \App\Services\ExcelExportHelper::streamDownload($filename, $headers, $rows);
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
            if ($request->filled('agency_name')) {
                $app->agency_name = $request->input('agency_name');
            }
            if ($request->filled('application_date')) {
                $app->application_date = $request->input('application_date');
            }
        }

        $app->status = 'Approved';
        $app->save();

        try {
            \App\Models\Notification::create([
                'title' => 'Application Approved',
                'message' => 'Application for "' . ($app->applicant_name ?? 'Applicant') . '" has been approved.',
                'url' => route('applications.category', $category)
            ]);
        } catch (\Throwable $e) {
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

        // Block rejection if application is assigned/connected to a project
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
        if ($projectModel && $projectModel::where('application_id', $app->id)->exists()) {
            return redirect()->back()->with('error', 'Assigned applications connected to a project cannot be rejected.');
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
        if (method_exists($dummyModel, 'address') && \Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
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

        // Fetch dropdown options for filters
        $pmUserIds = [];
        foreach ($projectModels as $pCatKey => $pModelClass) {
            if (class_exists($pModelClass)) {
                $pTbl = (new $pModelClass)->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($pTbl, 'project_manager_id')) {
                    $foundPmIds = \Illuminate\Support\Facades\DB::table($pTbl)->whereNotNull('project_manager_id')->pluck('project_manager_id')->toArray();
                    $pmUserIds = array_merge($pmUserIds, $foundPmIds);
                }
            }
        }
        $pmUserIds = array_values(array_unique(array_filter($pmUserIds)));

        $projectManagers = \App\Models\User::where(function($q) use ($pmUserIds) {
            $q->whereIn('role', ['project_manager', '3', 'Project Manager'])
              ->orWhereIn('id', $pmUserIds);
        })->orderBy('name', 'asc')->get();

        $addressStates = [];
        $addressDistricts = [];
        $selectedState = $request->input('state', 'all');

        foreach ($this->categories as $catKey => $catConf) {
            $catModel = $catConf['model'];
            if (class_exists($catModel)) {
                $tbl = (new $catModel)->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($tbl, 'state')) {
                    $st = \Illuminate\Support\Facades\DB::table($tbl)->whereNotNull('state')->where('state', '!=', '')->pluck('state')->toArray();
                    $addressStates = array_merge($addressStates, $st);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($tbl, 'district')) {
                    $dtQ = \Illuminate\Support\Facades\DB::table($tbl)->whereNotNull('district')->where('district', '!=', '');
                    if ($selectedState !== 'all' && !empty($selectedState) && \Illuminate\Support\Facades\Schema::hasColumn($tbl, 'state')) {
                        $dtQ->where('state', $selectedState);
                    }
                    $addressDistricts = array_merge($addressDistricts, $dtQ->pluck('district')->toArray());
                }
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('addresses')) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('addresses', 'state')) {
                $st = \Illuminate\Support\Facades\DB::table('addresses')->whereNotNull('state')->where('state', '!=', '')->pluck('state')->toArray();
                $addressStates = array_merge($addressStates, $st);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('addresses', 'district')) {
                $dtQ = \Illuminate\Support\Facades\DB::table('addresses')->whereNotNull('district')->where('district', '!=', '');
                if ($selectedState !== 'all' && !empty($selectedState) && \Illuminate\Support\Facades\Schema::hasColumn('addresses', 'state')) {
                    $dtQ->where('state', $selectedState);
                }
                $addressDistricts = array_merge($addressDistricts, $dtQ->pluck('district')->toArray());
            }
        }

        $states = array_values(array_unique(array_filter($addressStates)));
        sort($states);

        $districts = array_values(array_unique(array_filter($addressDistricts)));
        sort($districts);

        $agencyNames = [];
        foreach ($this->categories as $catKey => $catConf) {
            $catModel = $catConf['model'];
            if (class_exists($catModel)) {
                $tbl = (new $catModel)->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($tbl, 'agency_name')) {
                    $agencyNames = array_merge($agencyNames, \Illuminate\Support\Facades\DB::table($tbl)->whereNotNull('agency_name')->where('agency_name', '!=', '')->pluck('agency_name')->toArray());
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($tbl, 'agency_number')) {
                    $agencyNames = array_merge($agencyNames, \Illuminate\Support\Facades\DB::table($tbl)->whereNotNull('agency_number')->where('agency_number', '!=', '')->pluck('agency_number')->toArray());
                }
            }
        }
        $agencies = array_values(array_unique(array_filter($agencyNames)));
        sort($agencies);

        $pTypes = [];
        foreach ($projectModels as $pCatKey => $pModelClass) {
            if (class_exists($pModelClass)) {
                $pTbl = (new $pModelClass)->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($pTbl, 'type_of_project')) {
                    $pTypes = array_merge($pTypes, \Illuminate\Support\Facades\DB::table($pTbl)->whereNotNull('type_of_project')->where('type_of_project', '!=', '')->pluck('type_of_project')->toArray());
                }
            }
        }
        $projectTypes = array_values(array_unique(array_filter($pTypes)));
        sort($projectTypes);

        // Apply filters to applications list
        $pmIdParam = $request->input('pm_id', 'all');
        $agencyParam = $request->input('agency', 'all');
        $stateParam = $request->input('state', 'all');
        $districtParam = $request->input('district', 'all');
        $projectTypeParam = $request->input('type_of_project', 'all');
        $runningProjectParam = $request->input('running_project', 'all');

        $applications = $applications->filter(function ($appItem) use (
            $projectsMap,
            $pmIdParam,
            $agencyParam,
            $stateParam,
            $districtParam,
            $projectTypeParam,
            $runningProjectParam
        ) {
            $project = $projectsMap[$appItem->id] ?? null;
            $addr = (\Illuminate\Support\Facades\Schema::hasTable('applicant_addresses') && method_exists($appItem, 'address')) ? ($appItem->address ?? null) : null;

            // 1. Project Manager Filter
            if ($pmIdParam !== 'all' && !empty($pmIdParam)) {
                if (!$project || (string)$project->project_manager_id !== (string)$pmIdParam) {
                    return false;
                }
            }

            // 2. Agency Filter
            if ($agencyParam !== 'all' && !empty($agencyParam)) {
                $agencyVal = strtolower(trim($agencyParam));
                $appAgencyName = strtolower(trim($appItem->agency_name ?? ''));
                $appAgencyNum = strtolower(trim($appItem->agency_number ?? ''));
                $appAgency = strtolower(trim($appItem->agency ?? ''));
                $projAgency = strtolower(trim($project->agency ?? ''));

                if ($appAgencyName !== $agencyVal && $appAgencyNum !== $agencyVal && $appAgency !== $agencyVal && $projAgency !== $agencyVal) {
                    return false;
                }
            }

            // 3. State Filter
            if ($stateParam !== 'all' && !empty($stateParam)) {
                $stateVal = strtolower(trim($stateParam));
                $appState = strtolower(trim($addr->state ?? ($appItem->state ?? '')));
                if ($appState !== $stateVal) {
                    return false;
                }
            }

            // 4. District Filter
            if ($districtParam !== 'all' && !empty($districtParam)) {
                $districtVal = strtolower(trim($districtParam));
                $appDistrict = strtolower(trim($addr->district ?? ($appItem->district ?? '')));
                if ($appDistrict !== $districtVal) {
                    return false;
                }
            }

            // 5. Type of Project Filter
            if ($projectTypeParam !== 'all' && !empty($projectTypeParam)) {
                $pTypeVal = strtolower(trim($projectTypeParam));
                $projType = strtolower(trim($project->type_of_project ?? ($appItem->type_of_project ?? '')));
                if ($projType !== $pTypeVal) {
                    return false;
                }
            }

            // 6. Running Project Filter
            if ($runningProjectParam !== 'all' && !empty($runningProjectParam)) {
                $statusVal = strtolower(trim($project->status ?? ''));

                if ($runningProjectParam === 'completed') {
                    if (!$project || $statusVal !== 'completed') {
                        return false;
                    }
                } elseif ($runningProjectParam === 'running') {
                    if (!$project || empty($statusVal) || in_array($statusVal, ['completed', 'not set', 'not_set', 'none'])) {
                        return false;
                    }
                } elseif (in_array($runningProjectParam, ['not_set', 'not set'])) {
                    if ($project && !empty($statusVal) && !in_array($statusVal, ['not set', 'not_set', 'none'])) {
                        return false;
                    }
                }
            }

            return true;
        })->values();

        $viewName = str_replace('applications.', 'approved_applications.', $config['view']);

        $clusters = [];
        if (in_array($categorySlug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            $clusters = \App\Models\Cluster::orderBy('name', 'asc')->get();
        }
        $donors = \App\Models\Donor::orderBy('name', 'asc')->get();

        return view($viewName, compact('applications', 'categoryName', 'categorySlug', 'projectsMap', 'clusters', 'donors', 'projectManagers', 'agencies', 'states', 'districts', 'projectTypes'));
    }

    public function exportApproved(Request $request)
    {
        if (auth()->user() && !auth()->user()->canDownloadExcel()) {
            return redirect()->back()->with('error', 'Users with role "Others" cannot download Excel exports.');
        }

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
            if (method_exists($dummyModel, 'address') && \Illuminate\Support\Facades\Schema::hasTable('applicant_addresses')) {
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

        // Filter inputs for export
        $pmIdParam = $request->input('pm_id', 'all');
        $agencyParam = $request->input('agency', 'all');
        $stateParam = $request->input('state', 'all');
        $districtParam = $request->input('district', 'all');
        $projectTypeParam = $request->input('type_of_project', 'all');
        $runningProjectParam = $request->input('running_project', 'all');

        // Load project mappings for export items
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

        $projectsMap = [];
        $user = auth()->user();
        foreach ($allApprovedApps->groupBy('category_slug') as $catGroupSlug => $catApps) {
            $pModel = $projectModels[$catGroupSlug] ?? null;
            if ($pModel) {
                $appIds = $catApps->pluck('id')->toArray();
                $q = $pModel::with(['donor', 'projectManager'])->whereIn('application_id', $appIds);
                $catProjects = $this->scopeProjectsForUser($q, $user)->get()->keyBy('application_id');
                foreach ($catProjects as $appIdKey => $pObj) {
                    $projectsMap[$catGroupSlug . '_' . $appIdKey] = $pObj;
                }
            }
        }

        // Apply filters to export records
        $allApprovedApps = $allApprovedApps->filter(function ($appItem) use (
            $projectsMap,
            $pmIdParam,
            $agencyParam,
            $stateParam,
            $districtParam,
            $projectTypeParam,
            $runningProjectParam
        ) {
            $project = $projectsMap[$appItem->category_slug . '_' . $appItem->id] ?? null;
            $addr = $appItem->address ?? null;

            // 1. Project Manager Filter
            if ($pmIdParam !== 'all' && !empty($pmIdParam)) {
                if (!$project || (string)$project->project_manager_id !== (string)$pmIdParam) {
                    return false;
                }
            }

            // 2. Agency Filter
            if ($agencyParam !== 'all' && !empty($agencyParam)) {
                $agencyVal = strtolower(trim($agencyParam));
                $appAgencyName = strtolower(trim($appItem->agency_name ?? ''));
                $appAgencyNum = strtolower(trim($appItem->agency_number ?? ''));
                $appAgency = strtolower(trim($appItem->agency ?? ''));
                $projAgency = strtolower(trim($project->agency ?? ''));

                if ($appAgencyName !== $agencyVal && $appAgencyNum !== $agencyVal && $appAgency !== $agencyVal && $projAgency !== $agencyVal) {
                    return false;
                }
            }

            // 3. State Filter
            if ($stateParam !== 'all' && !empty($stateParam)) {
                $stateVal = strtolower(trim($stateParam));
                $appState = strtolower(trim($addr->state ?? ($appItem->state ?? '')));
                if ($appState !== $stateVal) {
                    return false;
                }
            }

            // 4. District Filter
            if ($districtParam !== 'all' && !empty($districtParam)) {
                $districtVal = strtolower(trim($districtParam));
                $appDistrict = strtolower(trim($addr->district ?? ($appItem->district ?? '')));
                if ($appDistrict !== $districtVal) {
                    return false;
                }
            }

            // 5. Type of Project Filter
            if ($projectTypeParam !== 'all' && !empty($projectTypeParam)) {
                $pTypeVal = strtolower(trim($projectTypeParam));
                $projType = strtolower(trim($project->type_of_project ?? ($appItem->type_of_project ?? '')));
                if ($projType !== $pTypeVal) {
                    return false;
                }
            }

            // 6. Running Project Filter
            if ($runningProjectParam !== 'all' && !empty($runningProjectParam)) {
                $statusVal = strtolower(trim($project->status ?? ''));

                if ($runningProjectParam === 'completed') {
                    if (!$project || $statusVal !== 'completed') {
                        return false;
                    }
                } elseif ($runningProjectParam === 'running') {
                    if (!$project || empty($statusVal) || in_array($statusVal, ['completed', 'not set', 'not_set', 'none'])) {
                        return false;
                    }
                } elseif (in_array($runningProjectParam, ['not_set', 'not set'])) {
                    if ($project && !empty($statusVal) && !in_array($statusVal, ['not set', 'not_set', 'none'])) {
                        return false;
                    }
                }
            }

            return true;
        })->values();

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

        $rows = [];
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
            $createdAt = $appItem->created_at ? (is_string($appItem->created_at) ? date('Y-m-d H:i:s', strtotime($appItem->created_at)) : $appItem->created_at->format('Y-m-d H:i:s')) : '';
            $updatedAt = $appItem->updated_at ? (is_string($appItem->updated_at) ? date('Y-m-d H:i:s', strtotime($appItem->updated_at)) : $appItem->updated_at->format('Y-m-d H:i:s')) : '';

            $row = [
                $appId,
                $this->formatCsvCell($regNo, 'reg_number'),
                $appItem->category_name,
                $appItem->applicant_name,
                $appItem->amount_requested,
                $appItem->status,
                $appItem->sponsor_status ?? 'N/A',
                $clusterCode,
                $clusterName,
                $this->formatCsvCell($appItem->agency_number ?? 'N/A', 'agency_number'),
                $appItem->contact_email ?? 'N/A',
                $this->formatCsvCell($contact1, 'contact_number_1'),
                $this->formatCsvCell($contact2, 'contact_number_2'),
                $houseName,
                $place,
                $postOffice,
                $town,
                $village,
                $panchayat,
                $district,
                $state,
                $this->formatCsvCell($pinCode, 'pin_code'),
                $appItem->details ?? ($appItem->additional_note ?? ''),
                $createdAt,
                $updatedAt
            ];

            $meta = $appItem->meta;
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            foreach ($metaKeys as $key) {
                $val = $meta[$key] ?? '';
                $row[] = $this->formatCsvCell($val, (string)$key);
            }

            $rows[] = $row;
        }

        $catNameStr = ($categoryParam !== 'all' && isset($this->categories[$categoryParam]))
            ? str_replace(' ', '_', strtolower($this->categories[$categoryParam]['name']))
            : 'all_categories';
        $filename = 'approved_applications_' . $catNameStr . '_' . date('Ymd_His') . '.xls';

        return \App\Services\ExcelExportHelper::streamDownload($filename, $headers, $rows);
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

        $category = $request->input('category') ?: $request->route('category') ?: $request->segment(3);
        $app = $this->findSocialAidApplication($id, $category);
        if (!$app) {
            if ($isJson) {
                return response()->json(['success' => false, 'error' => 'Application not found.'], 404);
            }
            return redirect()->back()->with('error', 'Application not found.');
        }

        try {
            $currentStatus = $app->sponsor_status ?? 'Not Sponsored';
            $sponsoredDate = $request->input('sponsored_date', date('Y-m-d'));

            if ($currentStatus === 'Sponsored' && !$request->has('sponsored_date')) {
                if (!$user->isSuperAdmin()) {
                    if ($isJson) {
                        return response()->json(['success' => false, 'error' => 'Only Super Admin can un-sponsor applications.'], 403);
                    }
                    return redirect()->back()->with('error', 'Only Super Admin can un-sponsor applications.');
                }

                $app->sponsor_status = 'Not Sponsored';
                if (\Illuminate\Support\Facades\Schema::hasColumn($app->getTable(), 'sponsorship_details')) {
                    $app->sponsorship_details = null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($app->getTable(), 'sponsored_date')) {
                    $app->sponsored_date = null;
                }
            } else {
                $app->sponsor_status = 'Sponsored';
                if (\Illuminate\Support\Facades\Schema::hasColumn($app->getTable(), 'sponsorship_details')) {
                    $app->sponsorship_details = $sponsoredDate;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($app->getTable(), 'sponsored_date')) {
                    $app->sponsored_date = $sponsoredDate;
                }
            }

            $app->save();

            if ($isJson) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sponsor status updated successfully.',
                    'sponsor_status' => $app->sponsor_status,
                    'sponsored_date' => $app->sponsorship_details ?? ($app->sponsored_date ?? $sponsoredDate)
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

    protected function formatCsvCell($value, string $keyName = '')
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        $strVal = is_array($value) ? json_encode($value) : (string) $value;
        $lowerKey = strtolower($keyName);

        $isPhoneOrIdKey = str_contains($lowerKey, 'contact') 
            || str_contains($lowerKey, 'mobile') 
            || str_contains($lowerKey, 'phone') 
            || str_contains($lowerKey, 'whatsapp') 
            || str_contains($lowerKey, 'aadhar') 
            || str_contains($lowerKey, 'pin') 
            || str_contains($lowerKey, 'reg');

        $trimmed = trim($strVal);
        $isNumericLong = (preg_match('/^\+?\d{8,20}$/', $trimmed) === 1) || (preg_match('/^0\d+$/', $trimmed) === 1);

        if ($isPhoneOrIdKey || $isNumericLong) {
            return '="' . $trimmed . '"';
        }

        return $strVal;
    }
}

