<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\User;
use App\Models\Contractor;
use App\Events\ProjectUpdated;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProjectController extends Controller
{
    private $groupedCategories = [
        'Construction Projects' => [
            'education-center' => [
                'name' => 'Education Center',
                'icon' => 'bx bxs-graduation',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\EducationCenterProject::class
            ],
            'cultural-center' => [
                'name' => 'Cultural Center',
                'icon' => 'bx bxs-landmark',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\CulturalCenterProject::class
            ],
            'hospital-or-clinics' => [
                'name' => 'Hospital or Clinics',
                'icon' => 'bx bxs-plus-medical',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\HospitalClinicProject::class
            ],
            'shops-and-others' => [
                'name' => 'Shops and Others',
                'icon' => 'bx bxs-store-alt',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\ShopOtherProject::class
            ],
            'house' => [
                'name' => 'House',
                'icon' => 'bx bxs-home',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\HouseProject::class
            ]
        ],
        'Drinking Water Projects' => [
            'drinking-water-group-level' => [
                'name' => 'Drinking Water - Group Level',
                'icon' => 'bx bx-water',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\DrinkingWaterGroupProject::class
            ],
            'drinking-water-individual-level' => [
                'name' => 'Drinking Water - Individual Level',
                'icon' => 'bx bxs-droplet',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\DrinkingWaterIndividualProject::class
            ]
        ],
        'Social Aid & Care' => [
            'orphan-care' => [
                'name' => 'Orphan Care',
                'icon' => 'bx bxs-face',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\OrphanCareProject::class
            ],
            'differently-abled' => [
                'name' => 'Differently Abled',
                'icon' => 'bx bx-accessibility',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\DifferentlyAbledProject::class
            ],
            'family-aid' => [
                'name' => 'Family Aid',
                'icon' => 'bx bxs-group',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\FamilyAidProject::class
            ]
        ],
        'General Schemes' => [
            'general' => [
                'name' => 'General',
                'icon' => 'bx bxs-file-blank',
                'bg' => 'linear-gradient(135deg, #10b981, #059669)',
                'model' => \App\Models\GeneralProject::class
            ]
        ]
    ];

    private $categories = [
        'education-center' => [
            'name' => 'Education Center',
            'view' => 'projects.education_center',
            'model' => \App\Models\EducationCenterProject::class
        ],
        'cultural-center' => [
            'name' => 'Cultural Center',
            'view' => 'projects.cultural_center',
            'model' => \App\Models\CulturalCenterProject::class
        ],
        'hospital-or-clinics' => [
            'name' => 'Hospital or Clinics',
            'view' => 'projects.hospital_clinics',
            'model' => \App\Models\HospitalClinicProject::class
        ],
        'shops-and-others' => [
            'name' => 'Shops and Others',
            'view' => 'projects.shops_others',
            'model' => \App\Models\ShopOtherProject::class
        ],
        'house' => [
            'name' => 'House',
            'view' => 'projects.house',
            'model' => \App\Models\HouseProject::class
        ],
        'drinking-water-group-level' => [
            'name' => 'Drinking Water - Group Level',
            'view' => 'projects.drinking_water_group',
            'model' => \App\Models\DrinkingWaterGroupProject::class
        ],
        'drinking-water-individual-level' => [
            'name' => 'Drinking Water - Individual Level',
            'view' => 'projects.drinking_water_individual',
            'model' => \App\Models\DrinkingWaterIndividualProject::class
        ],
        'orphan-care' => [
            'name' => 'Orphan Care',
            'view' => 'projects.orphan_care',
            'model' => \App\Models\OrphanCareProject::class
        ],
        'differently-abled' => [
            'name' => 'Differently Abled',
            'view' => 'projects.differently_abled',
            'model' => \App\Models\DifferentlyAbledProject::class
        ],
        'family-aid' => [
            'name' => 'Family Aid',
            'view' => 'projects.family_aid',
            'model' => \App\Models\FamilyAidProject::class
        ],
        'general' => [
            'name' => 'General',
            'view' => 'projects.general',
            'model' => \App\Models\GeneralProject::class
        ]
    ];



    private function resolveActiveCategory(Request $request)
    {
        $id = $request->route('id');
        $type = $request->query('type');

        // 1. Fallback to Referer header query string
        if (!$type) {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $query = parse_url($referer, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $queryParams);
                    $type = $queryParams['type'] ?? null;
                }
                
                // If referer is a category page, e.g., /admin/projects/category/cultural-center
                if (!$type) {
                    foreach ($this->categories as $slug => $config) {
                        if (str_contains($referer, "/category/{$slug}") || str_contains($referer, "/export/{$slug}")) {
                            $type = $slug;
                            break;
                        }
                    }
                }
            }
        }

        // 2. Fallback to Session
        if (!$type && $id) {
            $type = session('active_project_type_' . $id);
        }

        // Reorder categories array if type is identified
        if ($type) {
            $matchedSlug = null;
            foreach ($this->categories as $slug => $config) {
                if (strtolower($config['name']) === strtolower($type) || strtolower($slug) === strtolower($type)) {
                    $matchedSlug = $slug;
                    break;
                }
            }

            if ($matchedSlug) {
                // Move the matched category to the top of the array
                $matchedConfig = $this->categories[$matchedSlug];
                unset($this->categories[$matchedSlug]);
                $this->categories = [$matchedSlug => $matchedConfig] + $this->categories;

                // Sync the request query parameter
                $request->query->set('type', $matchedSlug);

                if ($id) {
                    session(['active_project_type_' . $id => $matchedConfig['name']]);
                }
            }
        }
    }

    public function index()
    {
        if (auth()->user() && auth()->user()->isReception()) {
            return redirect()->route('applications.index')->with('error', 'Unauthorized access.');
        }

        $user = auth()->user();
        $categories = $this->categories;
        $groupedCategories = $this->groupedCategories;

        if ($user && $user->isSocialAid()) {
            $socialAidSlugs = ['orphan-care', 'differently-abled', 'family-aid'];
            $categories = array_filter($categories, fn($key) => in_array($key, $socialAidSlugs), ARRAY_FILTER_USE_KEY);
            $groupedCategories = array_filter($groupedCategories, fn($key) => $key === 'Social Aid & Care', ARRAY_FILTER_USE_KEY);
        }

        $counts = [];
        foreach ($categories as $slug => $config) {
            $model = $config['model'];
            $counts[$config['name']] = $this->scopeProjectsForUser($model::query(), $user)->count();
        }

        return view('admin.projects_dashboard', [
            'categories' => $categories,
            'groupedCategories' => $groupedCategories,
            'counts' => $counts
        ]);
    }

    public function showCategory($slug)
    {
        if (auth()->user() && auth()->user()->isReception()) {
            return redirect()->route('applications.index')->with('error', 'Unauthorized access.');
        }

        if (auth()->user() && auth()->user()->isSocialAid() && !in_array($slug, ['orphan-care', 'differently-abled', 'family-aid'])) {
            return redirect()->route('projects.category', 'orphan-care')->with('error', 'Social Aid Manager can only access Social Aid projects.');
        }

        if (!array_key_exists($slug, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$slug];
        $categoryName = $config['name'];
        $categorySlug = $slug;
        $model = $config['model'];

        $user = auth()->user();
        $isOrphanCare = ($slug === 'orphan-care');
        $isSocialAid = in_array($slug, ['orphan-care', 'differently-abled', 'family-aid']);
        $relations = $isSocialAid ? ['application.cluster', 'application.address'] : ['donor', 'projectManager', 'engineer'];

        $projectQuery = $model::with($relations);
        if ($isSocialAid) {
            $projectQuery->orderByRaw("CASE 
                WHEN LOWER(status) = 'active' THEN 1 
                WHEN LOWER(status) = 'suspended' THEN 2 
                ELSE 3 
            END ASC");
        }
        $projectQuery->orderBy('created_at', 'desc');

        $projects = $this->scopeProjectsForUser($projectQuery, $user)->get();

        $donors = Donor::all();
        $managers = User::whereIn('role', [3, '3', 'project_manager', 'Project Manager'])
            ->orWhere('designation', 'like', '%project manager%')
            ->get();

        $engineers = User::whereIn('role', [6, '6', 'engineer', 'Engineer'])
            ->orWhere('designation', 'like', '%engineer%')
            ->get();

        $themes = \Illuminate\Support\Facades\DB::table('themes')->where('status', 1)->get();
        $subthemes = \Illuminate\Support\Facades\DB::table('subthemes')->where('status', 1)->get();

        return view($config['view'], compact(
            'categoryName',
            'categorySlug',
            'projects',
            'donors',
            'managers',
            'engineers',
            'themes',
            'subthemes'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $designationLower = strtolower($user->designation ?? '');
        $isCoo = ($user->isCoo() || $user->role == 2 || $designationLower === 'coo' || str_contains($designationLower, 'coo'));
        $isHod = ($user->isHod() || $user->role == 4 || $designationLower === 'hod' || str_contains($designationLower, 'hod'));
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin');
        if (!$isCoo && !$isHod && !$isSuperAdmin) {
            return redirect()->back()->with('error', 'Only HOD and COO are authorized to create projects.');
        }

        $redirectCategory = $request->input('redirect_category');
        $isOrphanCare = ($redirectCategory === 'orphan-care' || $request->input('type_of_project') === 'Orphan Care');

        $data = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'sponsor' => ['required', 'string', 'max:255'],
            'project_spec' => ['nullable', 'string'],
            'agency_project_no' => ['required', 'string', 'max:255'],
            'donor_id' => [$isOrphanCare ? 'nullable' : 'required', 'exists:donors,id'],
            'project_manager_id' => [$isOrphanCare ? 'nullable' : 'required', 'exists:users,id'],
            'engineer_id' => ['nullable', 'exists:users,id'],
            'unit' => ['nullable', 'string', 'max:255'],
            'available_budget' => [$isOrphanCare ? 'nullable' : 'required', 'numeric', 'min:0', 'max:9999999999999'],
            'type_of_project' => ['required', 'string'],
            'theme' => ['nullable', 'string', 'max:255'],
            'subtheme' => ['nullable', 'string', 'max:255'],
            'activity' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        if ($isOrphanCare) {
            unset($data['donor_id'], $data['project_manager_id'], $data['engineer_id'], $data['available_budget']);
        }

        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $data['type_of_project']) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            if (in_array($data['type_of_project'], ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'])) {
                $data['stage'] = 4; // Start at Stage 4 — Stages 1-3 are informational only
            } else {
                $data['stage'] = 6;
            }
            try {
                $projItem = $model::create($data);
                try {
                    \App\Models\Notification::create([
                        'title' => 'New Project',
                        'message' => 'A new project "' . $data['project_name'] . '" has been created under ' . $data['type_of_project'] . '.',
                        'url' => route('projects.category', $redirectCategory)
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Notification creation failed: ' . $e->getMessage());
                }
            } catch (QueryException $e) {
                $userMessage = 'Failed to save the project due to a database error.';
                if ($e->getCode() === '22003' || str_contains($e->getMessage(), 'Out of range')) {
                    $userMessage = 'The budget amount is too large. Please enter a value up to 9,999,999,999,999.';
                }
                return redirect()->back()->withInput()->with('error', $userMessage);
            }
            return redirect()->route('projects.category', $redirectCategory)->with('success', 'Project created successfully!');
        }

        abort(400, 'Invalid category');    
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $redirectCategory = $request->input('redirect_category');
        $isOrphanCare = ($redirectCategory === 'orphan-care' || $request->input('type_of_project') === 'Orphan Care');

        $data = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'sponsor' => ['required', 'string', 'max:255'],
            'project_spec' => ['nullable', 'string'],
            'agency_project_no' => ['required', 'string', 'max:255'],
            'donor_id' => [$isOrphanCare ? 'nullable' : 'required', 'exists:donors,id'],
            'project_manager_id' => [$isOrphanCare ? 'nullable' : 'required', 'exists:users,id'],
            'engineer_id' => ['nullable', 'exists:users,id'],
            'unit' => ['nullable', 'string', 'max:255'],
            'available_budget' => [$isOrphanCare ? 'nullable' : 'required', 'numeric', 'min:0', 'max:9999999999999'],
            'type_of_project' => ['required', 'string'],
            'theme' => ['nullable', 'string', 'max:255'],
            'subtheme' => ['nullable', 'string', 'max:255'],
            'activity' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        if ($isOrphanCare) {
            unset($data['donor_id'], $data['project_manager_id'], $data['engineer_id'], $data['available_budget']);
        }

        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $data['type_of_project']) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $project = $model::findOrFail($id);
            if (!$this->isPmOrEngineer($user, $project)) {
                return redirect()->back()->with('error', 'You are not authorized to edit this project.');
            }
            try {
                $project->update($data);
            } catch (QueryException $e) {
                $userMessage = 'Failed to update the project due to a database error.';
                if ($e->getCode() === '22003' || str_contains($e->getMessage(), 'Out of range')) {
                    $userMessage = 'The budget amount is too large. Please enter a value up to 9,999,999,999,999.';
                }
                return redirect()->back()->withInput()->with('error', $userMessage);
            }
            return redirect()->route('projects.category', $redirectCategory)->with('success', 'Project updated successfully!');
        }

        abort(400, 'Invalid category');
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasAdminAccess() && strtolower($user->designation ?? '') !== 'coo' && strtolower($user->designation ?? '') !== 'hod') {
            return redirect()->back()->with('error', 'You are not authorized to delete projects.');
        }

        $redirectCategory = $request->input('redirect_category');
        $config = $this->categories[$redirectCategory] ?? null;

        if (!$config) {
            foreach ($this->categories as $slug => $c) {
                if ($c['name'] === $request->input('type_of_project')) {
                    $config = $c;
                    $redirectCategory = $slug;
                    break;
                }
            }
        }

        if ($config) {
            $model = $config['model'];
            $project = $model::findOrFail($id);
            $project->delete();
            return redirect()->route('projects.category', $redirectCategory)->with('success', 'Project deleted successfully.');
        }

        abort(400, 'Invalid category');
    }



    private function isProjectLocked($project)
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return false;
        }

        $isSixStage = in_array($project->type_of_project, [
            'Education Center', 'Cultural Center', 'Hospital or Clinics', 
            'Shops and Others', 'House', 'Drinking Water - Group Level', 
            'Drinking Water - Individual Level', 'General'
        ]);

        if ($project->status === 'Completed') {
            return true;
        }

        if (!$isSixStage && $project->status === 'Approved') {
            return true;
        }

        return false;
    }

    private function getProjectInstance(Request $request, $id, $checkLock = false)
    {
        $slugsToModels = array_column($this->categories, 'model', 'name');
        
        // 1. Reorder categories array if type is identified from headers, query, or session
        $this->resolveActiveCategory($request);

        $user = auth()->user();
        $project = null;

        // 2. Try first category in reordered array
        $firstSlug = array_key_first($this->categories);
        if ($firstSlug) {
            $project = $this->scopeProjectsForUser($this->categories[$firstSlug]['model']::query(), $user)->find($id);
        }

        // 3. Fallback to parsing referer headers or routing clues
        if (!$project) {
            $referer = $request->headers->get('referer');
            if ($referer) {
                foreach ($this->categories as $slug => $config) {
                    if (str_contains($referer, "/category/{$slug}")) {
                        $project = $this->scopeProjectsForUser($config['model']::query(), $user)->find($id);
                        if ($project) {
                            session(['active_project_type_' . $id => $config['name']]);
                            break;
                        }
                    }
                }
            }
        }
        
        // 4. Ultimate fallback: loop through all models
        if (!$project) {
            foreach ($this->categories as $slug => $config) {
                $project = $this->scopeProjectsForUser($config['model']::query(), $user)->find($id);
                if ($project) {
                    session(['active_project_type_' . $id => $config['name']]);
                    break;
                }
            }
        }

        if ($project && $checkLock) {
            $user = auth()->user();
            if ($this->isProjectLocked($project)) {
                abort(403, 'This project is completed and locked for editing.');
            }
        }
        
        return $project;
    }

    public function show(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            abort(404);
        }

        $isOrphanCare = ($project instanceof \App\Models\OrphanCareProject || $project->type_of_project === 'Orphan Care');
        $relations = $isOrphanCare ? [] : ['donor', 'projectManager', 'engineer'];
        $project->load($relations);

        $appModel = str_replace('Project', 'Application', get_class($project));
        $application = null;
        $allApplications = [];
        if (class_exists($appModel)) {
            $projectModel = get_class($project);
            $assignedAppIds = $projectModel::whereNotNull('application_id')
                ->where('id', '!=', $project->id)
                ->pluck('application_id')
                ->toArray();

            $appQuery = $appModel::where('status', 'Approved')
                ->whereNotIn('id', $assignedAppIds);

            if ($project->type_of_project === 'Orphan Care') {
                $appQuery->where('sponsor_status', 'Sponsored');
            }

            $allApplications = $appQuery->orderBy('created_at', 'desc')->get();
            if ($project->application_id) {
                $application = $appModel::find($project->application_id);
            }
            if (!$application && !in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'])) {
                $application = $appModel::find($project->id) ?? $appModel::first();
            }
        }

        $allContractors = Contractor::orderBy('name')->get();

        $views = [
            'Education Center' => 'education_center',
            'Cultural Center' => 'cultural_center',
            'Hospital or Clinics' => 'hospital_clinics',
            'Shops and Others' => 'shops_others',
            'House' => 'house',
            'Drinking Water - Group Level' => 'drinking_water_group',
            'Drinking Water - Individual Level' => 'drinking_water_individual',
            'Orphan Care' => 'social_aid_project_detals',
            'Differently Abled' => 'social_aid_project_detals',
            'Family Aid' => 'social_aid_project_detals',
            'General' => 'general'
        ];

        $viewName = 'admin.project_detail';
        if (isset($views[$project->type_of_project])) {
            $candidate = 'admin.project_detail.' . $views[$project->type_of_project];
            if (view()->exists($candidate)) {
                $viewName = $candidate;
            } else {
                $candidateDirect = 'admin.' . $views[$project->type_of_project];
                if (view()->exists($candidateDirect)) {
                    $viewName = $candidateDirect;
                }
            }
        }

        return response()->view($viewName, compact('project', 'application', 'allApplications', 'allContractors'))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    public function assignApplication(Request $request, $id)
    {
        $user = auth()->user();
        
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $designationLower = strtolower($user->designation ?? '');
        $isCoo = ($user->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo'));
        $isHod = ($user->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod'));
        $isPm = ($user->isPm() || str_contains($designationLower, 'project manager') || $designationLower === 'project manager');
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin');

        $isSixStage = in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']);
        
        $isStage4Approved = false;
        if ($isSixStage) {
            $isStage4Approved = ($project->stage >= 5 || in_array($project->status, ['Approved', 'Completed']));
        }

        if ($isStage4Approved && !$isSuperAdmin && $project->type_of_project !== 'General') {
            return redirect()->back()->with('error', 'Once Stage 4 is approved, the assigned application cannot be changed.');
        }

        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only assigned Project Manager, Engineer, HOD, and COO are authorized to assign or connect applications.');
        }

        $request->validate([
            'application_id' => 'required'
        ]);

        $oldApplicationId = $project->application_id;
        $applicationId = $request->input('application_id');

        $appModels = [
            'Education Center' => \App\Models\EducationCenterApplication::class,
            'Cultural Center' => \App\Models\CulturalCenterApplication::class,
            'Hospital or Clinics' => \App\Models\HospitalClinicApplication::class,
            'Shops and Others' => \App\Models\ShopOtherApplication::class,
            'House' => \App\Models\HouseApplication::class,
            'Drinking Water - Group Level' => \App\Models\DrinkingWaterGroupApplication::class,
            'Drinking Water - Individual Level' => \App\Models\DrinkingWaterIndividualApplication::class,
            'Orphan Care' => \App\Models\OrphanCareApplication::class,
            'Differently Abled' => \App\Models\DifferentlyAbledApplication::class,
            'Family Aid' => \App\Models\FamilyAidApplication::class,
            'General' => \App\Models\GeneralApplication::class
        ];

        if ($applicationId) {
            $appClass = $appModels[$project->type_of_project] ?? null;
            if ($appClass) {
                $checkApp = $appClass::find($applicationId);
                if (!$checkApp) {
                    return redirect()->back()->with('error', 'The selected application does not exist.');
                }
                if ($checkApp->status !== 'Approved') {
                    return redirect()->back()->with('error', 'Only approved applications can be assigned.');
                }
                if ($project->type_of_project === 'Orphan Care' && ($checkApp->sponsor_status ?? '') !== 'Sponsored') {
                    return redirect()->back()->with('error', 'Only sponsored Orphan Care applications can be assigned.');
                }
            }
        }

        // Block if this application is already linked to a DIFFERENT project of the same type
        if ($applicationId && $applicationId != $oldApplicationId) {
            $projectModel = get_class($project);
            $alreadyUsed = $projectModel::where('application_id', $applicationId)
                ->where('id', '!=', $project->id)
                ->exists();

            if ($alreadyUsed) {
                return redirect()->back()->with('error', 'This application is already assigned to another project and cannot be assigned again.');
            }
        }

        $project->application_id = $applicationId;
        if (in_array($project->type_of_project, ['Drinking Water - Individual Level', 'Drinking Water - Group Level']) && $applicationId) {
            $project->status = 'Running';
            $statusRecord = $project->projectStatus;
            if (!$statusRecord) {
                $project->projectStatus()->create([
                    'status' => 'Running',
                    'status_custom' => null,
                ]);
            } else {
                $statusRecord->status = 'Running';
                $statusRecord->save();
            }
        }
        $project->save();

        $appModels = [
            'Education Center' => \App\Models\EducationCenterApplication::class,
            'Cultural Center' => \App\Models\CulturalCenterApplication::class,
            'Hospital or Clinics' => \App\Models\HospitalClinicApplication::class,
            'Shops and Others' => \App\Models\ShopOtherApplication::class,
            'House' => \App\Models\HouseApplication::class,
            'Drinking Water - Group Level' => \App\Models\DrinkingWaterGroupApplication::class,
            'Drinking Water - Individual Level' => \App\Models\DrinkingWaterIndividualApplication::class,
            'Orphan Care' => \App\Models\OrphanCareApplication::class,
            'Differently Abled' => \App\Models\DifferentlyAbledApplication::class,
            'Family Aid' => \App\Models\FamilyAidApplication::class,
            'General' => \App\Models\GeneralApplication::class
        ];
        
        $appClass = $appModels[$project->type_of_project] ?? null;
        if ($appClass) {
            // Revert old application status to Pending
            if ($oldApplicationId && $oldApplicationId != $applicationId) {
                $oldApp = $appClass::find($oldApplicationId);
                if ($oldApp) {
                    $oldApp->status = 'Pending';
                    $oldApp->save();
                }
            }
            
            // Set new application status to Approved
            if ($applicationId) {
                $newApp = $appClass::find($applicationId);
                if ($newApp) {
                    $newApp->status = 'Approved';
                    $newApp->save();
                }
            }
        }

        return redirect()->route('projects.show', $id)->with('success', 'Application connected to this project successfully!');
    }

    public function approveStage(Request $request, $id)
    {
        $user = auth()->user();
        $action = $request->input('action');

        $project = $this->getProjectInstance($request, $id, ($action !== 'reopen'));
        if (!$project) {
            abort(404);
        }

        if (!$action) {
            if ($project->stage <= 4) {
                $action = 'approve';
            } elseif ($project->stage == 5) {
                $action = 'promote_to_stage6';
            } elseif ($project->stage == 6) {
                $action = 'finalize_approval';
            }
        }

        $designationLower = strtolower($user->designation ?? '');
        $isCoo = ($user->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo'));
        $isHod = ($user->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod'));
        $isPm = ($user->isPm() || str_contains($designationLower, 'project manager') || $designationLower === 'project manager');
        $isEngineer = ($user->isEngineer() || str_contains($designationLower, 'engineer') || $designationLower === 'engineer');
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin');

        if ($action === 'reopen') {
            if (!$isSuperAdmin) {
                return redirect()->back()->with('error', 'Only Super Admin is authorized to reopen completed projects.');
            }
            $project->status = 'Approved';
            $project->stage = 6;
            $project->save();

            $statusRecord = $project->projectStatus;
            if ($statusRecord) {
                $statusRecord->coo_approved_at = null;
                $statusRecord->coo_approver_id = null;
                $statusRecord->coo_remarks = null;
                $statusRecord->save();
            }

            return redirect()->route('projects.show', $project->id)->with('success', 'Project reopened successfully.');
        }

        if ($project->type_of_project === 'General') {
            $currentStage = $project->stage;

            if ($currentStage <= 3) {
                if ($action === 'submit') {
                    if (!$isPm && !$isEngineer && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to submit projects for approval.');
                    }
                    $project->status = 'Pending Approval';
                    $project->save();
                    return redirect()->route('projects.show', $project->id)->with('success', 'Project submitted for HOD/COO approval.');
                }

                if ($action === 'approve') {
                    if (!$isCoo && !$isHod && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only COO or HOD is authorized to approve Stage 3.');
                    }
                    $project->stage = 4;
                    $project->status = 'Approved';
                    $project->save();
                    return redirect()->route('projects.show', $project->id)->with('success', 'Stage 3 approved successfully! Project promoted to Stage 4 (Expenses).');
                }

                if ($action === 'reject') {
                    if (!$isCoo && !$isHod && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only COO or HOD is authorized to reject Stage 3.');
                    }
                    $project->stage = 2;
                    $project->status = 'Rejected';
                    if ($request->input('remarks')) {
                        $project->remarks = $request->input('remarks');
                    }
                    $project->save();

                    return redirect()->route('projects.show', $project->id)->with('success', 'Project rejected and returned to Stage 2.');
                }
            }

            if ($currentStage == 4) {
                if ($action === 'promote_to_stage6' || $action === 'promote_to_stage5') {
                    if (!$isPm && !$isEngineer && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only Project Manager or Engineer is authorized to promote project to Stage 5.');
                    }
                    $project->stage = 5;
                    $project->save();
                    return redirect()->route('projects.show', $project->id)->with('success', 'Project promoted to Stage 5 (Completion Stage) successfully!');
                }
            }

            if ($currentStage >= 5 && $action === 'finalize_approval') {
                $project->status = 'Completed';
                $project->stage = 5;
                $project->save();

                $statusRecord = $project->projectStatus;
                if (!$statusRecord) {
                    $statusRecord = $project->projectStatus()->create([
                        'user_id' => $user->id,
                        'designation' => $user->designation ?? 'User'
                    ]);
                }
                $statusRecord->coo_approved_at = now();
                $statusRecord->coo_approver_id = $user->id;
                $statusRecord->save();

                return redirect()->route('projects.show', $project->id)->with('success', 'Project marked as Completed successfully.');
            }
        }

        if (in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level'])) {
            $currentStage = $project->stage;

            if ($currentStage == 3 && $action === 'submit_corrections') {
                if (!$isPm && !$isEngineer && !$isSuperAdmin) {
                    return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to submit corrections.');
                }
                $project->stage = 4;
                $project->status = 'Pending';
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Corrections submitted! Project promoted to Stage 4.');
            }

            if ($currentStage <= 4) {
                if ($action === 'submit') {
                    if (!$isPm && !$isEngineer && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to submit projects for approval.');
                    }
                    $project->status = 'Pending Approval';
                    $project->save();
                    return redirect()->route('projects.show', $project->id)->with('success', 'Project submitted for HOD/COO approval.');
                }

                if ($action === 'approve') {
                    if ($project->type_of_project !== 'General' && !$isCoo && !$isHod && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only COO or HOD is authorized to approve Stage 4.');
                    }
                    $project->stage = 5;
                    $project->status = 'Approved';
                    $project->save();
                    return redirect()->route('projects.show', $project->id)->with('success', 'Stage 4 approved successfully! Project promoted to Stage 5.');
                }

                if ($action === 'reject') {
                    if ($project->type_of_project !== 'General' && !$isCoo && !$isHod && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only COO or HOD is authorized to reject Stage 4.');
                    }
                    $project->stage = 3;
                    $project->status = 'Rejected';
                    $project->save();

                    if ($request->input('remarks')) {
                        $project->remarks = $request->input('remarks');
                        $project->save();
                    }

                    return redirect()->route('projects.show', $project->id)->with('success', 'Project rejected and returned to Stage 3 for corrections.');
                }

                return redirect()->back()->with('error', 'Invalid action for Stage 4.');
            }

            $isIndividualWater = ($project->type_of_project === 'Drinking Water - Individual Level');
            $isGroupWater = ($project->type_of_project === 'Drinking Water - Group Level');
            $isWaterProject = ($isIndividualWater || $isGroupWater);

            if ($currentStage == 5) {
                if ($isWaterProject && $action === 'finalize_approval') {
                    if (!$isCoo && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only COO is authorized to perform final approval.');
                    }

                    $project->status = 'Completed';
                    $project->save();

                    $statusRecord = $project->projectStatus;
                    if (!$statusRecord) {
                        $statusRecord = $project->projectStatus()->create([
                            'status' => null,
                            'status_custom' => null,
                        ]);
                    }
                    $statusRecord->status = 'Completed';
                    $statusRecord->coo_approved_at = now();
                    $statusRecord->coo_approver_id = auth()->id();
                    $statusRecord->coo_remarks = $request->input('remarks');
                    $statusRecord->save();

                    return redirect()->route('projects.show', $project->id)->with('success', 'Project completely approved and finalized as COMPLETED by COO!');
                }

                if ($action === 'promote_to_stage6') {
                    if (!$isPm && !$isEngineer && !$isSuperAdmin) {
                        return redirect()->back()->with('error', 'Only Project Manager or Engineer is authorized to promote project to Stage 6.');
                    }
                    $project->stage = 6;
                    $project->save();
                    return redirect()->route('projects.show', $project->id)->with('success', 'Project promoted to Stage 6 (Completion Stage) successfully!');
                }
                return redirect()->back()->with('error', 'Invalid action for Stage 5.');
            }

            if ($currentStage == 6 && $action === 'finalize_approval') {
                if ($project->type_of_project !== 'General' && !$isCoo && !$isSuperAdmin) {
                    return redirect()->back()->with('error', 'Only COO is authorized to perform final approval.');
                }

                $docRecord = $project->files_with_timestamps;
                $hasCompCert = ($docRecord && $docRecord->completion_certificate && $docRecord->completion_certificate !== '0');
                $hasMeasBook = ($docRecord && $docRecord->measurement_book && $docRecord->measurement_book !== '0');

                if ((!$isIndividualWater && !$hasCompCert) || (!$isGroupWater && !$hasMeasBook)) {
                    return redirect()->back()->with('error', 'Required completion documents (Stage 6) must be uploaded before final approval.');
                }

                $project->status = 'Completed';
                $project->save();

                $statusRecord = $project->projectStatus;
                if (!$statusRecord) {
                    $statusRecord = $project->projectStatus()->create([
                        'status' => null,
                        'status_custom' => null,
                    ]);
                }
                $statusRecord->status = 'Completed';
                $statusRecord->coo_approved_at = now();
                $statusRecord->coo_approver_id = auth()->id();
                $statusRecord->coo_remarks = $request->input('remarks');
                $statusRecord->save();

                return redirect()->route('projects.show', $project->id)->with('success', 'Project completely approved and finalized as COMPLETED by COO!');
            }

            return redirect()->back()->with('error', 'Invalid stage progression.');
        }

        if (!$isCoo && !$isSuperAdmin) {
            return redirect()->back()->with('error', 'Only COO is authorized to approve projects.');
        }
        
        $project->status = 'Approved';
        $project->stage = 6;
        $project->save();

        $statusRecord = $project->projectStatus;
        if (!$statusRecord) {
            $statusRecord = $project->projectStatus()->create([
                'status' => null,
                'status_custom' => null,
            ]);
        }
        $statusRecord->status = 'Approved';
        $statusRecord->coo_approved_at = now();
        $statusRecord->coo_approver_id = auth()->id();
        $statusRecord->coo_remarks = $request->input('remarks');
        $statusRecord->save();

        return redirect()->route('projects.show', $id)->with('success', 'Project completely approved and finalized by COO!');
    }

    public function export(Request $request, $category)
    {
        if (!array_key_exists($category, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$category];
        $model = $config['model'];
        $user = auth()->user();
        $isOrphanCare = ($category === 'orphan-care');
        $isSocialAid = in_array($category, ['orphan-care', 'differently-abled', 'family-aid']);

        $relations = ['application.cluster', 'application.address', 'donor', 'projectManager', 'funds'];
        $projects = $this->scopeProjectsForUser($model::with($relations), $user)->get();

        // Apply filters if passed from frontend
        if ($request->filled('state') && strtolower($request->state) !== 'all') {
            $stFilter = strtolower(trim($request->state));
            $projects = $projects->filter(function($p) use ($stFilter) {
                $app = $p->application;
                $meta = $app->meta ?? [];
                $st = strtolower(trim($app?->address?->state ?? ($app?->state ?? ($meta['state'] ?? ''))));
                return $st === $stFilter;
            });
        }

        if ($request->filled('district') && strtolower($request->district) !== 'all') {
            $dtFilter = strtolower(trim($request->district));
            $projects = $projects->filter(function($p) use ($dtFilter) {
                $app = $p->application;
                $meta = $app->meta ?? [];
                $dt = strtolower(trim($app?->address?->district ?? ($app?->district ?? ($meta['district'] ?? ''))));
                return $dt === $dtFilter;
            });
        }

        if ($request->filled('agency') && strtolower($request->agency) !== 'all') {
            $agFilter = strtolower(trim($request->agency));
            $projects = $projects->filter(function($p) use ($agFilter) {
                $app = $p->application;
                $meta = $app->meta ?? [];
                $ag = strtolower(trim(
                    $app?->agency_name 
                    ?? ($meta['agency_name'] ?? null) 
                    ?? ($p->donor?->name ?? null) 
                    ?? ($p->agency ?? null) 
                    ?? ($p->funds?->first()?->donor ?? null) 
                    ?? ($p->funds?->first()?->agency ?? null) 
                    ?? ($p->sponsor && $p->sponsor !== 'Sponsored' ? $p->sponsor : '')
                ));
                return $ag === $agFilter;
            });
        }

        if ($request->filled('cluster') && strtolower($request->cluster) !== 'all') {
            $clFilter = strtolower(trim($request->cluster));
            $projects = $projects->filter(function($p) use ($clFilter) {
                $app = $p->application;
                $meta = $app->meta ?? [];
                $cl = strtolower(trim($app?->cluster?->name ?? ($meta['cluster'] ?? '')));
                return $cl === $clFilter;
            });
        }

        if ($request->filled('gender') && strtolower($request->gender) !== 'all') {
            $gnFilter = strtolower(trim($request->gender));
            $projects = $projects->filter(function($p) use ($gnFilter) {
                $app = $p->application;
                $meta = $app->meta ?? [];
                $gn = strtolower(trim($app?->gender ?? ($meta['gender'] ?? '')));
                return $gn === $gnFilter;
            });
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower(trim($request->search));
            $projects = $projects->filter(function($p) use ($searchTerm) {
                $app = $p->application;
                $meta = $app->meta ?? [];
                $searchable = strtolower(implode(' ', array_filter([
                    $p->project_id,
                    $p->project_name,
                    $p->agency_project_no,
                    $p->sponsor,
                    $p->remarks,
                    $app?->applicant_name,
                    $app?->father_name ?? ($meta['father_name'] ?? null),
                    $app?->mother_name ?? ($meta['mother_name'] ?? null),
                    $app?->place ?? ($meta['place'] ?? null),
                    $app?->district ?? ($meta['district'] ?? null),
                    $app?->state ?? ($meta['state'] ?? null),
                    $app?->mobile_1 ?? ($meta['mobile_1'] ?? null),
                ])));
                return str_contains($searchable, $searchTerm);
            });
        }

        if ($isOrphanCare) {
            $headers = [
                'Project ID',
                'Agency Project No',
                'Agency Name',
                'Orphan / Beneficiary Name',
                'Application ID',
                'Application Date',
                'Father Name',
                'Father Death Date',
                'Father Death Cause',
                'Grandfather Name',
                'Mother Name',
                'Mother Alive Status',
                'Mother Death Date',
                'Mother Death Cause',
                'Mother Remarried Status',
                "Mother's Father Name",
                'Guardian Name',
                'Guardian Relation',
                'Gender',
                'Age',
                'Date of Birth',
                'Mobile 1',
                'Mobile 2',
                'WhatsApp Number',
                'Contact Email',
                'Aadhar Number',
                'House Name',
                'House Type',
                'Place',
                'Post Office',
                'Pin Code',
                'Town',
                'District',
                'State',
                'Cluster',
                'School Name',
                'School Class',
                'Madrassa Name',
                'Madrassa Class',
                'Not Studying Reason',
                'Health Status',
                'Monthly Income',
                'Monthly Expense',
                'Siblings Male',
                'Siblings Female',
                'Siblings Total',
                'Amount Requested',
                'Sponsorship Details',
                'Sponsor Status',
                'Current Beneficiaries',
                'Recommender Name',
                'Recommender Org',
                'Recommender Phone',
                'Recommender Position',
                'Additional Note',
                'Theme',
                'Subtheme',
                'Activity',
                'Project Spec',
                'Unit',
                'Stage',
                'Status',
                'Remarks',
                'Created At'
            ];
        } elseif ($isSocialAid) {
            $headers = [
                'Project ID',
                'Agency Project No',
                'Agency Name',
                'Orphan / Beneficiary Name',
                'Application ID',
                'Father Name',
                'Mother Name',
                'Guardian Name',
                'Guardian Relation',
                'Gender',
                'Age',
                'Date of Birth',
                'Mobile 1',
                'Mobile 2',
                'WhatsApp Number',
                'Contact Email',
                'Aadhar Number',
                'House Name',
                'Place',
                'Post Office',
                'Pin Code',
                'Town',
                'District',
                'State',
                'Cluster',
                'School Name',
                'School Class',
                'Madrassa Name',
                'Madrassa Class',
                'Health Status',
                'Monthly Income',
                'Monthly Expense',
                'Sponsor Status',
                'Theme',
                'Subtheme',
                'Activity',
                'Project Spec',
                'Unit',
                'Stage',
                'Status',
                'Remarks',
                'Created At'
            ];
        } else {
            $headers = [
                'Project ID',
                'Agency Project No',
                'Project Name',
                'Donor Name',
                'Project Manager',
                'Available Budget',
                'Type of Project',
                'Application ID',
                'Applicant Name',
                'Father Name',
                'Mother Name',
                'Mobile',
                'Place',
                'District',
                'State',
                'Cluster',
                'Remarks',
                'Stage',
                'Status',
                'Created At'
            ];
        }

        $callback = function() use ($projects, $headers, $isSocialAid, $isOrphanCare, $config) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);

            foreach ($projects as $project) {
                $app = $project->application;
                $appMeta = $app->meta ?? [];

                $agencyName = $app?->agency_name 
                    ?? ($appMeta['agency_name'] ?? null) 
                    ?? ($project->donor?->name ?? null) 
                    ?? ($project->agency ?? null) 
                    ?? ($project->funds?->first()?->donor ?? null) 
                    ?? ($project->funds?->first()?->agency ?? null) 
                    ?? ($project->sponsor && $project->sponsor !== 'Sponsored' ? $project->sponsor : 'N/A');

                if ($isOrphanCare) {
                    $appId = $app?->application_id ?? ($app ? 'APLRCFI' . $app->id : 'N/A');
                    $addr = $app?->address;
                    fputcsv($file, [
                        $project->project_id,
                        $project->agency_project_no ?? ($app?->agency_number ?? ($appMeta['agency_number'] ?? 'N/A')),
                        $agencyName,
                        $project->project_name ?? ($app?->applicant_name ?? ($appMeta['applicant_name'] ?? 'N/A')),
                        $appId,
                        $app?->application_date ?? ($appMeta['application_date'] ?? ($app?->created_at ? $app->created_at->format('Y-m-d') : 'N/A')),
                        $app?->father_name ?? ($appMeta['father_name'] ?? 'N/A'),
                        $app?->father_death_date ?? ($appMeta['father_death_date'] ?? 'N/A'),
                        $app?->father_death_cause ?? ($appMeta['father_death_cause'] ?? 'N/A'),
                        $app?->grandfather_name ?? ($appMeta['grandfather_name'] ?? 'N/A'),
                        $app?->mother_name ?? ($appMeta['mother_name'] ?? 'N/A'),
                        $app?->mother_alive_status ?? ($appMeta['mother_alive_status'] ?? 'N/A'),
                        $app?->mother_death_date ?? ($appMeta['mother_death_date'] ?? 'N/A'),
                        $app?->mother_death_cause ?? ($appMeta['mother_death_cause'] ?? 'N/A'),
                        $app?->mother_remarried_status ?? ($appMeta['mother_remarried_status'] ?? 'N/A'),
                        $app?->mothers_father_name ?? ($appMeta['mothers_father_name'] ?? 'N/A'),
                        $app?->guardian_name ?? ($appMeta['guardian_name'] ?? 'N/A'),
                        $app?->guardian_relation ?? ($appMeta['guardian_relation'] ?? 'N/A'),
                        $app?->gender ?? ($appMeta['gender'] ?? 'N/A'),
                        $app?->age ?? ($appMeta['age'] ?? 'N/A'),
                        $app?->dob ?? ($appMeta['dob'] ?? 'N/A'),
                        $app?->mobile_1 ?? ($appMeta['mobile_1'] ?? ($appMeta['contact_number_1'] ?? 'N/A')),
                        $app?->mobile_2 ?? ($appMeta['mobile_2'] ?? ($appMeta['contact_number_2'] ?? 'N/A')),
                        $app?->whatsapp_number ?? ($appMeta['whatsapp_number'] ?? 'N/A'),
                        $app?->contact_email ?? ($appMeta['contact_email'] ?? 'N/A'),
                        $app?->aadhar_number ?? ($appMeta['aadhar_number'] ?? 'N/A'),
                        $addr?->house_name ?? ($app?->house_name ?? ($appMeta['house_name'] ?? 'N/A')),
                        $app?->house_type ?? ($appMeta['house_type'] ?? 'N/A'),
                        $addr?->place ?? ($app?->place ?? ($appMeta['place'] ?? 'N/A')),
                        $addr?->post_office ?? ($app?->post_office ?? ($appMeta['post_office'] ?? 'N/A')),
                        $addr?->pin_code ?? ($app?->pin_code ?? ($appMeta['pin_code'] ?? 'N/A')),
                        $app?->town ?? ($appMeta['town'] ?? 'N/A'),
                        $addr?->district ?? ($app?->district ?? ($appMeta['district'] ?? 'N/A')),
                        $addr?->state ?? ($app?->state ?? ($appMeta['state'] ?? 'N/A')),
                        $app?->cluster?->name ?? ($appMeta['cluster'] ?? 'N/A'),
                        $app?->school_name ?? ($appMeta['school_name'] ?? 'N/A'),
                        $app?->school_class ?? ($appMeta['school_class'] ?? 'N/A'),
                        $app?->madrassa_name ?? ($appMeta['madrassa_name'] ?? 'N/A'),
                        $app?->madrassa_class ?? ($appMeta['madrassa_class'] ?? 'N/A'),
                        $app?->not_studying_reason ?? ($appMeta['not_studying_reason'] ?? 'N/A'),
                        $app?->health_status ?? ($appMeta['health_status'] ?? 'N/A'),
                        $app?->monthly_income ?? ($appMeta['monthly_income'] ?? 'N/A'),
                        $app?->monthly_expense ?? ($appMeta['monthly_expense'] ?? 'N/A'),
                        $app?->siblings_male ?? ($appMeta['siblings_male'] ?? 'N/A'),
                        $app?->siblings_female ?? ($appMeta['siblings_female'] ?? 'N/A'),
                        $app?->siblings_total ?? ($appMeta['siblings_total'] ?? 'N/A'),
                        $app?->amount_requested ?? ($appMeta['amount_requested'] ?? 'N/A'),
                        $app?->sponsorship_details ?? ($appMeta['sponsorship_details'] ?? 'N/A'),
                        $app?->sponsor_status ?? ($project->sponsor ?? 'N/A'),
                        $app?->current_beneficiaries ?? ($appMeta['current_beneficiaries'] ?? 'N/A'),
                        $app?->recommender_name ?? ($appMeta['recommender_name'] ?? 'N/A'),
                        $app?->recommender_org ?? ($appMeta['recommender_org'] ?? 'N/A'),
                        $app?->recommender_phone ?? ($appMeta['recommender_phone'] ?? 'N/A'),
                        $app?->recommender_position ?? ($appMeta['recommender_position'] ?? 'N/A'),
                        $app?->additional_note ?? ($appMeta['additional_note'] ?? 'N/A'),
                        $project->theme ?? 'N/A',
                        $project->subtheme ?? 'N/A',
                        $project->activity ?? 'N/A',
                        $project->project_spec ?? 'N/A',
                        $project->unit ?? 'N/A',
                        'Stage ' . $project->stage,
                        $project->status ?? 'Active',
                        $project->remarks ?? 'N/A',
                        $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                } elseif ($isSocialAid) {
                    $appId = $app?->application_id ?? ($app ? 'APLRCFI' . $app->id : 'N/A');
                    fputcsv($file, [
                        $project->project_id,
                        $project->agency_project_no ?? ($app?->agency_number ?? ($appMeta['agency_number'] ?? 'N/A')),
                        $agencyName,
                        $project->project_name ?? ($app?->applicant_name ?? ($appMeta['applicant_name'] ?? 'N/A')),
                        $appId,
                        $app?->father_name ?? ($appMeta['father_name'] ?? 'N/A'),
                        $app?->mother_name ?? ($appMeta['mother_name'] ?? 'N/A'),
                        $appMeta['guardian_name'] ?? 'N/A',
                        $appMeta['guardian_relation'] ?? 'N/A',
                        $app?->gender ?? ($appMeta['gender'] ?? 'N/A'),
                        $appMeta['age'] ?? 'N/A',
                        $appMeta['dob'] ?? 'N/A',
                        $app?->mobile_1 ?? ($appMeta['mobile_1'] ?? ($appMeta['contact_number_1'] ?? 'N/A')),
                        $app?->mobile_2 ?? ($appMeta['mobile_2'] ?? ($appMeta['contact_number_2'] ?? 'N/A')),
                        $appMeta['whatsapp_number'] ?? 'N/A',
                        $appMeta['contact_email'] ?? 'N/A',
                        $appMeta['aadhar_number'] ?? 'N/A',
                        $appMeta['house_name'] ?? 'N/A',
                        $app?->place ?? ($appMeta['place'] ?? 'N/A'),
                        $appMeta['post_office'] ?? 'N/A',
                        $appMeta['pin_code'] ?? 'N/A',
                        $appMeta['town'] ?? 'N/A',
                        $app?->district ?? ($appMeta['district'] ?? 'N/A'),
                        $app?->state ?? ($appMeta['state'] ?? 'N/A'),
                        $app?->cluster?->name ?? ($appMeta['cluster'] ?? 'N/A'),
                        $appMeta['school_name'] ?? 'N/A',
                        $appMeta['school_class'] ?? 'N/A',
                        $appMeta['madrassa_name'] ?? 'N/A',
                        $appMeta['madrassa_class'] ?? 'N/A',
                        $appMeta['health_status'] ?? 'N/A',
                        $appMeta['monthly_income'] ?? 'N/A',
                        $appMeta['monthly_expense'] ?? 'N/A',
                        $app?->sponsor_status ?? ($project->sponsor ?? 'N/A'),
                        $project->theme ?? 'N/A',
                        $project->subtheme ?? 'N/A',
                        $project->activity ?? 'N/A',
                        $project->project_spec ?? 'N/A',
                        $project->unit ?? 'N/A',
                        'Stage ' . $project->stage,
                        $project->status ?? 'Active',
                        $project->remarks ?? 'N/A',
                        $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                } else {
                    $appId = $app?->application_id ?? ($app ? 'APLRCFI' . $app->id : 'N/A');
                    fputcsv($file, [
                        $project->project_id,
                        $project->agency_project_no ?? 'N/A',
                        $project->project_name ?? 'N/A',
                        $project->donor ? $project->donor->name : ($agencyName !== 'N/A' ? $agencyName : 'N/A'),
                        $project->projectManager ? $project->projectManager->name : 'N/A',
                        $project->available_budget ?? '0',
                        $project->type_of_project ?? $config['name'],
                        $appId,
                        $app?->applicant_name ?? ($project->project_name ?? 'N/A'),
                        $app?->father_name ?? ($appMeta['father_name'] ?? 'N/A'),
                        $app?->mother_name ?? ($appMeta['mother_name'] ?? 'N/A'),
                        $app?->mobile_1 ?? ($appMeta['mobile_1'] ?? 'N/A'),
                        $app?->place ?? ($appMeta['place'] ?? 'N/A'),
                        $app?->district ?? ($appMeta['district'] ?? 'N/A'),
                        $app?->state ?? ($appMeta['state'] ?? 'N/A'),
                        $app?->cluster?->name ?? ($appMeta['cluster'] ?? 'N/A'),
                        $project->remarks ?? 'N/A',
                        'Stage ' . $project->stage,
                        $project->status ?? 'Active',
                        $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
            }

            fclose($file);
        };

        $filename = str_replace(' ', '_', strtolower($config['name'])) . '_projects_full_' . date('Ymd_His') . '.csv';

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function uploadFile(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only assigned Project Manager, Engineer, HOD, and COO are authorized to add files.');
        }
        if ($this->isProjectLocked($project)) {
            return redirect()->back()->with('error', 'Project is finalized and locked.');
        }
        $request->validate([
            'document_name' => 'required|string',
            'file' => 'required|file|max:10240' // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            
            // Save file in public/uploads/projects/{project_id}/
            $docNameClean = str_replace(' ', '_', strtolower($request->input('document_name')));
            $filename = $docNameClean . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
            
            $uploadedFile->move(public_path('uploads/projects/' . $project->id), $filename);
            
            $docName = $request->input('document_name');
            $column = \App\Models\ProjectDocument::$docColumnMap[$docName] ?? null;
            if ($column) {
                $docRecord = $project->projectDocument;
                if (!$docRecord) {
                    $docRecord = $project->projectDocument()->create();
                }
                $docRecord->$column = 'uploads/projects/' . $project->id . '/' . $filename;
                $timeColumn = $column . '_ticked_at';
                $docRecord->$timeColumn = now();
                $docRecord->save();
            }
            return redirect()->route('projects.show', $id)->with('success', $request->input('document_name') . ' uploaded successfully!');
        }

        return redirect()->back()->with('error', 'File upload failed.');
    }

    public function toggleFile(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, false);
        if (!$project) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Project not found.'], 404);
            }
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Only Project Manager and Engineer are authorized to toggle checklist.'], 403);
            }
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to toggle checklist.');
        }

        if ($this->isProjectLocked($project)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Project is finalized and locked.'], 403);
            }
            return redirect()->back()->with('error', 'Project is finalized and locked.');
        }

        $docName = $request->input('document_name') ?? $request->input('file_key') ?? $request->input('field');
        if (!$docName) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Document name is required.'], 400);
            }
            return redirect()->back()->with('error', 'Document name is required.');
        }

        $column = \App\Models\ProjectDocument::$docColumnMap[$docName] ?? null;
        $ticked = false;
        $tickedAtStr = '';
        $msg = '';

        if ($column && method_exists($project, 'projectDocument')) {
            try {
                $docRecord = $project->projectDocument;
                if (!$docRecord) {
                    $docRecord = $project->projectDocument()->create();
                }

                $currentVal = $docRecord->$column;
                if ($currentVal && $currentVal !== '0') {
                    if ($currentVal !== '1') {
                        $physicalPath = public_path($currentVal);
                        if (file_exists($physicalPath) && is_file($physicalPath)) {
                            @unlink($physicalPath);
                        }
                    }
                    $docRecord->$column = '0';
                    $timeColumn = $column . '_ticked_at';
                    $docRecord->$timeColumn = null;
                    $ticked = false;
                    $msg = "$docName unticked.";
                } else {
                    $docRecord->$column = '1';
                    $timeColumn = $column . '_ticked_at';
                    $now = now();
                    $docRecord->$timeColumn = $now;
                    $ticked = true;
                    $tickedAtStr = $now->timezone('Asia/Kolkata')->format('d-M-Y h:i A');
                    $msg = "$docName ticked.";
                }
                $docRecord->save();
            } catch (\Exception $e) {}
        }

        // Also save to $files['checklist'] for full fallback support
        $files = $project->files ?? [];
        $fileChecklist = $files['checklist'] ?? [];
        $docKey = str_replace(' ', '_', strtolower($docName));

        if (!$msg) {
            $currentStatus = isset($fileChecklist[$docKey]) ? ($fileChecklist[$docKey]['ticked'] ?? false) : false;
            $ticked = !$currentStatus;
            $tickedAtStr = $ticked ? date('d-M-Y H:i') : '';
            $msg = $ticked ? "$docName ticked." : "$docName unticked.";
        }

        $fileChecklist[$docKey] = [
            'name' => $docName,
            'ticked' => $ticked,
            'ticked_at' => $tickedAtStr,
            'ticked_by' => $ticked ? ($user->name ?? 'Admin') : null
        ];
        $files['checklist'] = $fileChecklist;
        $project->files = $files;
        $project->save();

        try {
            ProjectUpdated::dispatch($project->id, 'files', auth()->id(), 'toggle_file', [
                'document_name' => $docName,
                'ticked' => $ticked,
                'ticked_at' => $tickedAtStr
            ]);
        } catch (\Exception $e) {}

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => $msg, 
                'ticked' => $ticked,
                'is_ticked' => $ticked,
                'ticked_at' => $tickedAtStr ?: '-'
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function updateMapLink(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only authorized staff are allowed to update map link.');
        }

        $request->validate([
            'location_map_link' => 'nullable|url|max:1000',
        ]);

        $docRecord = $project->projectDocument;
        if (!$docRecord) {
            $insertData = [];
            foreach (\App\Models\ProjectDocument::$docColumnMap as $docName => $column) {
                $insertData[$column] = '0';
                $insertData[$column . '_ticked_at'] = null;
            }
            $docRecord = $project->projectDocument()->create($insertData);
        }

        $docRecord->location_map_link = $request->input('location_map_link');
        $docRecord->save();

        return redirect()->back()->with('success', 'Location map link updated successfully!');
    }

    public function updatePhase(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Project not found.'], 404);
            }
            abort(404);
        }

        $user = auth()->user();
        $isCoo = ($user->isCoo() || strtolower($user->designation ?? '') === 'coo');
        $isHod = ($user->isHod() || strtolower($user->designation ?? '') === 'hod');
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin');

        if (!$isCoo && !$isHod && !$isSuperAdmin) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Only COO and HOD are authorized to update project status.'], 403);
            }
            return redirect()->back()->with('error', 'Only COO and HOD are authorized to update project status.');
        }

        $request->validate([
            'project_phase'        => 'required|string',
            'project_phase_custom' => 'nullable|string|max:255',
        ]);

        if (empty($project->application_id)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please connect an application first.'], 400);
            }
            return redirect()->back()->with('error', 'Please connect an application first.');
        }

        $phase = $request->input('project_phase');
        $custom = ($phase === 'Other') ? trim($request->input('project_phase_custom', '')) : null;

        $statusRecord = $project->projectStatus;
        if (!$statusRecord) {
            $statusRecord = $project->projectStatus()->create([
                'status' => null,
                'status_custom' => null,
            ]);
        }
        $statusRecord->status = $phase;
        $statusRecord->status_custom = $custom;
        $statusRecord->save();

        $updatedAt = $statusRecord->updated_at ? \Carbon\Carbon::parse($statusRecord->updated_at)->timezone('Asia/Kolkata') : now();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project status updated to "' . ($phase === 'Other' ? $custom : $phase) . '".',
                'phase'   => $phase,
                'custom'  => $custom,
                'updated_at' => $updatedAt->format('d-M-Y h:i A'),
                'updated_human' => $updatedAt->diffForHumans(),
            ]);
        }

        return redirect()->back()->with('success', 'Project status updated successfully.');
    }

    public function addMaterial(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage materials.');
        }

        if (empty($project->application_id) && $project->type_of_project !== 'General') {
            return redirect()->back()->with('error', 'Please connect an application first.');
        }

        $request->validate([
            'material' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $materials = $project->materials;
        if (empty($materials)) {
            $materials = [];
        }

        $materials[] = [
            'material' => $request->input('material'),
            'amount' => (float)$request->input('amount')
        ];

        $project->materials = $materials;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Material added successfully!');
    }

    public function updateMaterial(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage materials.');
        }

        $request->validate([
            'material' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $materials = $project->materials;
        if (empty($materials)) {
            $materials = [];
        }

        if (isset($materials[$index])) {
            $materials[$index] = [
                'material' => $request->input('material'),
                'amount' => (float)$request->input('amount')
            ];
            $project->materials = $materials;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Material updated successfully!');
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    public function deleteMaterial(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage materials.');
        }

        $materials = $project->materials;
        if (empty($materials)) {
            $materials = [];
        }

        if (isset($materials[$index])) {
            array_splice($materials, $index, 1);
            $project->materials = $materials;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Material deleted successfully!');
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    public function addCommunityContribution(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage community contributions.');
        }

        $request->validate([
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $files = $project->files ?? [];
        $commContribs = $files['community_contributions'] ?? [];
        if (!is_array($commContribs)) {
            $commContribs = [];
        }

        $commContribs[] = [
            'item' => $request->input('item'),
            'amount' => (float)$request->input('amount')
        ];

        $files['community_contributions'] = $commContribs;
        
        // Sync total sum to completion_details
        $compDetails = $files['completion_details'] ?? [];
        $commTotalSum = 0.0;
        foreach ($commContribs as $c) {
            $commTotalSum += floatval($c['amount'] ?? 0);
        }
        $compDetails['community_contribution'] = $commTotalSum;
        $files['completion_details'] = $compDetails;

        $project->files = $files;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Community contribution added successfully!');
    }

    public function updateCommunityContribution(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage community contributions.');
        }

        $request->validate([
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $files = $project->files ?? [];
        $commContribs = $files['community_contributions'] ?? [];
        if (!is_array($commContribs)) {
            $commContribs = [];
        }

        if (isset($commContribs[$index])) {
            $commContribs[$index] = [
                'item' => $request->input('item'),
                'amount' => (float)$request->input('amount')
            ];
            
            // Sync total sum to completion_details
            $compDetails = $files['completion_details'] ?? [];
            $commTotalSum = 0.0;
            foreach ($commContribs as $c) {
                $commTotalSum += floatval($c['amount'] ?? 0);
            }
            $compDetails['community_contribution'] = $commTotalSum;
            $files['completion_details'] = $compDetails;
            
            $files['community_contributions'] = $commContribs;
            $project->files = $files;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Community contribution updated successfully!');
        }

        return redirect()->back()->with('error', 'Item not found.');
    }

    public function deleteCommunityContribution(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage community contributions.');
        }

        $files = $project->files ?? [];
        $commContribs = $files['community_contributions'] ?? [];
        if (!is_array($commContribs)) {
            $commContribs = [];
        }

        if (isset($commContribs[$index])) {
            array_splice($commContribs, $index, 1);
            
            // Sync total sum to completion_details
            $compDetails = $files['completion_details'] ?? [];
            $commTotalSum = 0.0;
            foreach ($commContribs as $c) {
                $commTotalSum += floatval($c['amount'] ?? 0);
            }
            $compDetails['community_contribution'] = $commTotalSum;
            $files['completion_details'] = $compDetails;
            
            $files['community_contributions'] = $commContribs;
            $project->files = $files;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Community contribution deleted successfully!');
        }

        return redirect()->back()->with('error', 'Item not found.');
    }

    public function addExpense(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage expenses.');
        }

        if (empty($project->application_id) && $project->type_of_project !== 'General') {
            return redirect()->back()->with('error', 'Please connect an application first.');
        }

        $request->validate([
            'material_index' => 'nullable|integer',
            'comm_index' => 'nullable|integer',
            'expense_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0'
        ]);

        $expenses = $project->expenses;
        if (empty($expenses)) {
            $expenses = [];
        }

        $expenses[] = [
            'material_index' => $request->filled('material_index') ? (int)$request->input('material_index') : null,
            'comm_index' => $request->filled('comm_index') ? (int)$request->input('comm_index') : null,
            'expense_name' => $request->input('expense_name'),
            'quantity' => (float)$request->input('quantity'),
            'amount' => (float)$request->input('amount')
        ];

        $project->expenses = $expenses;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Expense added successfully!');
    }

    public function updateExpense(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage expenses.');
        }

        $request->validate([
            'material_index' => 'nullable|integer',
            'comm_index' => 'nullable|integer',
            'expense_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0'
        ]);

        $expenses = $project->expenses;
        if (empty($expenses)) {
            $expenses = [];
        }

        if (isset($expenses[$index])) {
            $expenses[$index] = [
                'material_index' => $request->filled('material_index') ? (int)$request->input('material_index') : null,
                'comm_index' => $request->filled('comm_index') ? (int)$request->input('comm_index') : null,
                'expense_name' => $request->input('expense_name'),
                'quantity' => (float)$request->input('quantity'),
                'amount' => (float)$request->input('amount')
            ];
            $project->expenses = $expenses;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Expense updated successfully!');
        }

        return redirect()->back()->with('error', 'Expense not found.');
    }

    public function deleteExpense(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage expenses.');
        }

        $expenses = $project->expenses;
        if (empty($expenses)) {
            $expenses = [];
        }

        if (isset($expenses[$index])) {
            array_splice($expenses, $index, 1);
            $project->expenses = $expenses;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Expense deleted successfully!');
        }

        return redirect()->back()->with('error', 'Expense not found.');
    }

    public function uploadPhoto(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, false);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to add photos.');
        }

        $request->validate([
            'photo' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240', // 10MB max
            'category' => 'nullable|string|in:before,starting,inbetween,after,banner,stone,inauguration'
        ]);

        if ($project->type_of_project === 'General') {
            $files = $project->files ?? [];
            $totalGeneralPhotos = count(array_unique(array_merge(
                $files['photos_after'] ?? ($files['photos'] ?? []),
                $files['photos_before'] ?? [],
                $files['photos_inbetween'] ?? [],
                $files['photos_inauguration'] ?? []
            )));
            if ($totalGeneralPhotos >= 3) {
                return redirect()->back()->with('error', 'Maximum limit reached. General projects can have a maximum of 3 photos.');
            }
        }

        if ($request->hasFile('photo')) {
            $uploadedFile = $request->file('photo');
            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $ext = 'jpg';
            }
            $filename = 'photo_' . time() . '_' . uniqid() . '.' . $ext;
            $targetPath = public_path('uploads/projects/' . $project->id . '/' . $filename);
            $this->compressAndSaveImage($uploadedFile, $targetPath, 2 * 1024 * 1024);
            
            $files = $project->files ?? [];
            $rawCategory = $request->input('category') ?: ($request->query('category') ?: 'after');
            $category = str_replace('photos_', '', strtolower(trim($rawCategory)));
            if (!in_array($category, ['before', 'starting', 'inbetween', 'after', 'banner', 'stone', 'inauguration'])) {
                $category = 'after';
            }
            $key = 'photos_' . $category;
            
            // Enforce maximum 1 photo per column (replace previous photo if present)
            $existing = array_merge((array)($files[$key] ?? []), (array)($files[$category . '_photos'] ?? []));
            foreach ($existing as $oldPhotoPath) {
                if ($oldPhotoPath && file_exists(public_path($oldPhotoPath))) {
                    @unlink(public_path($oldPhotoPath));
                }
            }

            $newPhotoPath = 'uploads/projects/' . $project->id . '/' . $filename;
            
            $files[$key] = [$newPhotoPath];
            $files[$category . '_photos'] = [$newPhotoPath];
            
            if ($category === 'after') {
                $files['photos'] = [$newPhotoPath];
            }

            $project->files = $files;
            $project->save();

            $photoIndex = 0;
            $deleteUrl = route('projects.delete_photo', [$project->id, 0]) . '?category=' . $category;

            try {
                ProjectUpdated::dispatch($project->id, $category, auth()->id(), 'upload_photo', [
                    'category' => $category,
                    'photo_url' => asset($newPhotoPath),
                    'photo_index' => 0,
                    'delete_url' => $deleteUrl,
                    'total_photos' => 1
                ]);
            } catch (\Exception $e) {}

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploaded successfully!',
                    'category' => $category,
                    'photo_url' => asset($newPhotoPath),
                    'photo_index' => 0,
                    'delete_url' => $deleteUrl,
                    'total_photos' => 1
                ]);
            }

            return redirect()->route('projects.show', $id)->with('success', 'Photo uploaded successfully!');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Photo upload failed.'], 400);
        }

        return redirect()->back()->with('error', 'Photo upload failed.');
    }

    public function deletePhoto(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, false);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only Project Manager and Engineer are authorized to manage photos.'], 403);
            }
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage photos.');
        }

        $rawCategory = $request->input('category') ?: ($request->query('category') ?: 'after');
        $category = str_replace('photos_', '', strtolower(trim($rawCategory)));
        if (!in_array($category, ['before', 'starting', 'inbetween', 'after', 'banner', 'stone', 'inauguration'])) {
            $category = 'after';
        }

        $key = 'photos_' . $category;
        $files = $project->files ?? [];

        // Collect all possible keys where photo paths might be stored
        $possibleKeys = [$key, $category . '_photos', $category];
        if ($category === 'after') {
            $possibleKeys[] = 'photos';
        }

        $photoPaths = [];

        // 1. Check in $files accessor
        foreach ($possibleKeys as $pk) {
            if (!empty($files[$pk])) {
                $val = $files[$pk];
                $arr = is_array($val) ? $val : [$val];
                foreach ($arr as $p) {
                    if (!empty($p) && is_string($p)) {
                        $photoPaths[] = $p;
                    }
                }
            }
        }

        // 2. Check in ProjectPhoto model directly
        $projectPhoto = $project->projectPhoto;
        if ($projectPhoto) {
            foreach ($possibleKeys as $pk) {
                if (!empty($projectPhoto->$pk)) {
                    $raw = $projectPhoto->$pk;
                    $decoded = is_array($raw) ? $raw : json_decode($raw, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $p) {
                            if (!empty($p) && is_string($p)) {
                                $photoPaths[] = $p;
                            }
                        }
                    } elseif (is_string($raw) && !empty($raw)) {
                        $photoPaths[] = $raw;
                    }
                }
            }
        }

        $photoPaths = array_values(array_unique($photoPaths));

        // Delete physical files found
        foreach ($photoPaths as $photoPath) {
            $filepath = public_path($photoPath);
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
        }

        // Wipe out key across $files array
        foreach ($possibleKeys as $pk) {
            $files[$pk] = [];
        }

        $project->files = $files;
        $project->save();

        // Directly clear columns on ProjectPhoto model
        if ($projectPhoto) {
            foreach ($possibleKeys as $pk) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('project_photos', $pk)) {
                    $projectPhoto->$pk = null;
                }
            }
            $projectPhoto->save();
        }

        try {
            ProjectUpdated::dispatch($project->id, $category, auth()->id(), 'delete_photo', [
                'category' => $category,
                'photo_index' => 0,
                'total_photos' => 0
            ]);
        } catch (\Exception $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully!',
                'category' => $category,
                'photo_index' => 0,
                'total_photos' => 0
            ]);
        }

        return redirect()->back()->with('success', 'Photo deleted successfully!');
    }

    public function saveCompletionDetails(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only Project Manager and Engineer are authorized to manage completion details.'], 403);
            }
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage completion details.');
        }

        $request->validate([
            'total_project_cost' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid_by_donor' => 'required|numeric|min:0',
            'community_contribution' => 'required|numeric|min:0',
            'any_other' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0'
        ]);

        $files = $project->files ?? [];
        $files['completion_details'] = [
            'total_project_cost' => (float)$request->input('total_project_cost'),
            'total_amount' => (float)$request->input('total_amount'),
            'amount_paid_by_donor' => (float)$request->input('amount_paid_by_donor'),
            'community_contribution' => (float)$request->input('community_contribution'),
            'any_other' => (float)$request->input('any_other'),
            'deductions' => (float)$request->input('deductions')
        ];
        
        $files['community_contributions'] = [
            ['item' => 'Community Contribution', 'amount' => (float)$request->input('community_contribution')],
            ['item' => 'Other', 'amount' => (float)$request->input('any_other')]
        ];
        
        $project->files = $files;
        $project->save();

        try {
            ProjectUpdated::dispatch($project->id, 'financials', auth()->id(), 'save_completion_details', $files['completion_details']);
        } catch (\Exception $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Completion details saved successfully!']);
        }

        return redirect()->route('projects.show', $id)->with('success', 'Completion details saved successfully!');
    }

    public function addContractor(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage contractor details.');
        }

        if (empty($project->application_id) && $project->type_of_project !== 'General') {
            return redirect()->back()->with('error', 'Please connect an application first.');
        }

        $data = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'type_of_contract' => 'required|string|max:255',
            'purpose_of_contract' => 'required|string'
        ]);

        $files = $project->files ?? [];
        $contractors = $files['contractors'] ?? [];
        $contractors[] = $data;
        $files['contractors'] = $contractors;
        $project->files = $files;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Contractor added successfully!');
    }

    public function updateContractor(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage contractor details.');
        }

        $data = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'type_of_contract' => 'required|string|max:255',
            'purpose_of_contract' => 'required|string'
        ]);

        $files = $project->files ?? [];
        $contractors = $files['contractors'] ?? [];
        
        if (isset($contractors[$index])) {
            $contractors[$index] = $data;
            $files['contractors'] = $contractors;
            $project->files = $files;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Contractor updated successfully!');
        }

        return redirect()->back()->with('error', 'Contractor not found.');
    }

    public function deleteContractor(Request $request, $id, $index)
    {
        $project = $this->getProjectInstance($request, $id, true);
        if (!$project) {
            abort(404);
        }

        $user = auth()->user();
        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage contractor details.');
        }

        $files = $project->files ?? [];
        $contractors = $files['contractors'] ?? [];
        
        if (isset($contractors[$index])) {
            array_splice($contractors, $index, 1);
            $files['contractors'] = $contractors;
            $project->files = $files;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Contractor deleted successfully!');
        }

        return redirect()->back()->with('error', 'Contractor not found.');
    }

    private function isPmOrEngineer($user, $project = null)
    {
        if (!$user) {
            return false;
        }

        $designationLower = strtolower($user->designation ?? '');
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin' || str_contains($designationLower, 'admin'));
        $isCoo = ($user->isCoo() || $user->role == 2 || $user->role === 'coo' || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo'));
        $isHod = ($user->isHod() || $user->role == 4 || $user->role === 'hod' || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod'));
        $isPm = ($user->isPm() || $user->role == 3 || $user->role === 'project_manager' || str_contains($designationLower, 'project manager') || $designationLower === 'project manager');
        $isEngineer = ($user->isEngineer() || $user->role == 6 || $user->role === 'engineer' || str_contains($designationLower, 'engineer') || $designationLower === 'engineer');
        $isSocialAid = ($user->isSocialAid() || $user->role == 8 || $user->role === 'social_aid' || str_contains($designationLower, 'social aid'));

        if ($isSuperAdmin || $isCoo || $isHod || $isPm || $isEngineer || $isSocialAid) {
            return true;
        }

        if ($project) {
            if (isset($project->project_manager_id) && $project->project_manager_id == $user->id) {
                return true;
            }
            if (isset($project->engineer_id) && $project->engineer_id == $user->id) {
                return true;
            }
        }

        return false;
    }

    private function scopeProjectsForUser($query, $user)
    {
        if (!$user) {
            return $query;
        }

        $isPm = ($user->isPm() || strtolower($user->designation ?? '') === 'project manager');
        $isEngineer = ($user->isEngineer() || strtolower($user->designation ?? '') === 'engineer');
        $isSuperAdmin = ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin');
        $isCoo = ($user->isCoo() || strtolower($user->designation ?? '') === 'coo');
        $isHod = ($user->isHod() || strtolower($user->designation ?? '') === 'hod');

        if ($isSuperAdmin || $isCoo || $isHod) {
            return $query;
        }

        if ($user->isSocialAid()) {
            $table = $query->getModel()->getTable();
            $isSocialAidTable = in_array($table, ['orphan_care_projects', 'differently_abled_projects', 'family_aid_projects']);
            if ($isSocialAidTable) {
                return $query;
            }
            return $query->whereRaw('1 = 0');
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

    public function addInspection(Request $request, $id)
    {
        $user = auth()->user();
        $project = $this->getProjectInstance($request, $id, false);
        if (!$project) {
            abort(404);
        }

        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage inspections.');
        }

        if ($this->isProjectLocked($project)) {
            return redirect()->back()->with('error', 'Project is finalized and locked.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $project->projectInspections()->create([
            'name' => $request->name,
            'designation' => $request->designation,
            'date' => $request->date,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('projects.show', $id)->with('success', 'Inspection report added successfully!');
    }

    public function updateInspection(Request $request, $id, $inspectionId)
    {
        $user = auth()->user();
        $project = $this->getProjectInstance($request, $id, false);
        if (!$project) {
            abort(404);
        }

        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage inspections.');
        }

        if ($this->isProjectLocked($project)) {
            return redirect()->back()->with('error', 'Project is finalized and locked.');
        }

        $inspection = $project->projectInspections()->findOrFail($inspectionId);

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $inspection->update([
            'name' => $request->name,
            'designation' => $request->designation,
            'date' => $request->date,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('projects.show', $id)->with('success', 'Inspection report updated successfully!');
    }

    public function deleteInspection(Request $request, $id, $inspectionId)
    {
        $user = auth()->user();
        $project = $this->getProjectInstance($request, $id, false);
        if (!$project) {
            abort(404);
        }

        if (!$this->isPmOrEngineer($user, $project)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage inspections.');
        }

        if ($this->isProjectLocked($project)) {
            return redirect()->back()->with('error', 'Project is finalized and locked.');
        }

        $inspection = $project->projectInspections()->findOrFail($inspectionId);
        $inspection->delete();

        return redirect()->route('projects.show', $id)->with('success', 'Inspection report deleted successfully!');
    }

    private function getSocialAidProjectAndApp($id)
    {
        $map = [
            \App\Models\OrphanCareProject::class => \App\Models\OrphanCareApplication::class,
            \App\Models\DifferentlyAbledProject::class => \App\Models\DifferentlyAbledApplication::class,
            \App\Models\FamilyAidProject::class => \App\Models\FamilyAidApplication::class,
        ];

        foreach ($map as $pModel => $appModel) {
            $project = $pModel::find($id);
            if ($project) {
                $application = $project->application_id ? $appModel::find($project->application_id) : null;
                return [$project, $application];
            }
        }

        return [null, null];
    }

    public function socialAidUploadPhoto(Request $request, $id)
    {
        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }
        if (!$application) {
            return redirect()->back()->with('error', 'No application is linked to this project.');
        }

        $request->validate([
            'student_photo' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
        ]);

        if ($request->hasFile('student_photo')) {
            if ($application->student_photo) {
                $oldPath = public_path($application->student_photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $uploadedFile = $request->file('student_photo');
            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $ext = 'jpg';
            }
            $filename = 'student_' . time() . '_' . uniqid() . '.' . $ext;
            $targetPath = public_path('uploads/students/' . $application->id . '/' . $filename);
            $this->compressAndSaveImage($uploadedFile, $targetPath, 2 * 1024 * 1024);

            $application->student_photo = 'uploads/students/' . $application->id . '/' . $filename;
            $application->save();

            return redirect()->back()->with('success', 'Photo uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Photo upload failed.');
    }

    public function socialAidDeletePhoto(Request $request, $id)
    {
        $user = auth()->user();
        $designationLower = strtolower($user->designation ?? '');
        $isSuperAdmin = ($user && ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin'));
        $isCoo = ($user && ($user->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo')));
        $isHod = ($user && ($user->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod')));

        if (!$isSuperAdmin && !$isCoo && !$isHod) {
            return redirect()->back()->with('error', 'Unauthorized Action: Only HOD, COO, and Super Admin can delete photos.');
        }

        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }
        if (!$application) {
            return redirect()->back()->with('error', 'No application is linked to this project.');
        }

        if ($application->student_photo) {
            $path = public_path($application->student_photo);
            if (file_exists($path)) {
                @unlink($path);
            }
            $application->student_photo = null;
            $application->save();

            return redirect()->back()->with('success', 'Photo deleted successfully!');
        }

        return redirect()->back()->with('error', 'No photo found to delete.');
    }

    public function socialAidUpdateAddress(Request $request, $id)
    {
        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }

        $validatedData = $request->validate([
            'applicant_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'dob' => 'nullable|string|max:255',
            'age' => 'nullable|string|max:255',
            'aadhar_number' => 'nullable|string|max:255',
            'health_status' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'grandfather_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mothers_father_name' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:255',
            'father_death_date' => 'nullable|string|max:255',
            'father_death_cause' => 'nullable|string|max:255',
            'mother_alive_status' => 'nullable|string|max:255',
            'mother_remarried_status' => 'nullable|string|max:255',
            'mother_death_date' => 'nullable|string|max:255',
            'mother_death_cause' => 'nullable|string|max:255',
            'siblings_male' => 'nullable|string|max:255',
            'siblings_female' => 'nullable|string|max:255',
            'siblings_total' => 'nullable|string|max:255',
            'current_beneficiaries' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|string|max:255',
            'monthly_expense' => 'nullable|string|max:255',
            'sponsorship_details' => 'nullable|string|max:255',
            'house_type' => 'nullable|string|max:255',
            'school_name' => 'nullable|string|max:255',
            'school_class' => 'nullable|string|max:255',
            'madrassa_name' => 'nullable|string|max:255',
            'madrassa_class' => 'nullable|string|max:255',
            'not_studying_reason' => 'nullable|string|max:255',
            'house_name' => 'nullable|string|max:255',
            'place' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'additional_note' => 'nullable|string',
            'post_office' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'panchayat' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|string|max:255',
            'mobile_2' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'contact_number_1' => 'nullable|string|max:255',
            'contact_number_2' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'recommender_name' => 'nullable|string|max:255',
            'recommender_org' => 'nullable|string|max:255',
            'recommender_position' => 'nullable|string|max:255',
            'recommender_phone' => 'nullable|string|max:255',
            'agency_number' => 'nullable|string|max:255',
            'agency_name' => 'nullable|string|max:255',
            'application_date' => 'nullable|string|max:255',
        ]);

        // 1. Update Project Level fields
        $projTable = $project->getTable();
        if ($request->has('location')) {
            $locVal = $request->input('location');
            if (\Illuminate\Support\Facades\Schema::hasColumn($projTable, 'location')) {
                $project->location = $locVal;
            }
            $project->save();
        }

        $noteVal = $request->input('remarks') ?? $request->input('additional_note');
        if ($noteVal !== null) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($projTable, 'remarks')) {
                $project->remarks = $noteVal;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn($projTable, 'additional_note')) {
                $project->additional_note = $noteVal;
            }
            $project->save();
        }

        // 2. Update Application Level fields if application exists
        if ($application) {
            $contact1 = $request->input('mobile_1') ?? ($request->input('contact_number_1') ?? $request->input('mobile'));
            $contact2 = $request->input('mobile_2') ?? $request->input('contact_number_2');

            $table = $application->getTable();
            $updateData = [];
            foreach ($validatedData as $key => $val) {
                if ($val !== null && \Illuminate\Support\Facades\Schema::hasColumn($table, $key)) {
                    $updateData[$key] = $val;
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'mobile_1')) {
                $updateData['mobile_1'] = $contact1;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'mobile_2')) {
                $updateData['mobile_2'] = $contact2;
            }

            if ($noteVal !== null) {
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'remarks')) {
                    $updateData['remarks'] = $noteVal;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'additional_note')) {
                    $updateData['additional_note'] = $noteVal;
                }
            }

            if (!empty($updateData)) {
                $application->update($updateData);
            }
            if (method_exists($application, 'setMetaAttribute')) {
                $currentMeta = $application->meta ?? [];
                $application->meta = array_merge($currentMeta, array_filter($validatedData, fn($v) => $v !== null));
                $application->save();
            }

            if (method_exists($application, 'address')) {
                $application->address()->updateOrCreate([], array_filter([
                    'contact_number_1' => $contact1,
                    'contact_number_2' => $contact2,
                    'house_name' => $validatedData['house_name'] ?? null,
                    'place' => $validatedData['place'] ?? null,
                    'post_office' => $validatedData['post_office'] ?? null,
                    'village' => $validatedData['village'] ?? null,
                    'panchayat' => $validatedData['panchayat'] ?? null,
                    'district' => $validatedData['district'] ?? null,
                    'state' => $validatedData['state'] ?? null,
                ], fn($v) => $v !== null));
            }
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $project->refresh();
            if ($application) {
                $application->refresh();
            }
            $loc = $project->location ?? ($application->location ?? null);
            $rem = $project->remarks ?? ($application->remarks ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Details updated successfully!',
                'location' => $loc,
                'remarks' => $rem,
                'project' => $project,
                'application' => $application,
            ]);
        }

        return redirect()->back()->with('success', 'Details updated successfully!');
    }

    public function socialAidAddFund(Request $request, $id)
    {
        $user = auth()->user();
        $designationLower = strtolower($user->designation ?? '');
        $isSuperAdmin = ($user && ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin'));
        $isCoo = ($user && ($user->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo')));
        $isHod = ($user && ($user->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod')));
        $isSocialAid = ($user && ((method_exists($user, 'isSocialAid') ? $user->isSocialAid() : false) || in_array($user->role, [8, '8', 'social_aid', 'Social Aid', 'Social Aid Manager']) || str_contains($designationLower, 'social aid')));

        if (!$isSuperAdmin && !$isCoo && !$isHod) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized: Only HOD, COO, and Super Admin can add financial data.'], 403);
            }
            return back()->with('error', 'Unauthorized Action: Only HOD, COO, and Super Admin can add financial data.');
        }

        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }

        $donorVal = $request->input('donor') ?? $request->input('agency');
        if (str_contains($donorVal, ' (')) {
            $donorVal = trim(explode(' (', $donorVal)[0]);
        }

        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|numeric',
            'ifsc_number' => 'nullable|string|max:255',
        ]);

        if (empty($donorVal)) {
            return back()->withErrors(['donor' => 'Donor field is required.']);
        }

        // Prevent duplicate creation if an identical fund record was created within the last 5 seconds
        $existingDuplicate = $project->funds()
            ->where('date', $request->input('date'))
            ->where('amount', (float)$request->input('amount'))
            ->where('donor', $donorVal)
            ->where('created_at', '>=', now()->subSeconds(5))
            ->first();

        if ($existingDuplicate) {
            $fund = $existingDuplicate;
        } else {
            $fund = $project->funds()->create([
                'date' => $request->input('date'),
                'amount' => (float)$request->input('amount'),
                'donor' => $donorVal,
                'account_name' => $request->input('account_name'),
                'account_number' => $request->input('account_number'),
                'ifsc_number' => $request->input('ifsc_number'),
                'agency_project_no' => $project->agency_project_no ?? null,
            ]);
        }

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fund transfer record added successfully!',
                'fund' => $fund,
                'formatted_date' => !empty($fund->date) ? date('d-M-Y', strtotime($fund->date)) : 'N/A',
                'formatted_amount' => number_format($fund->amount, 2),
                'total_amount' => number_format($project->funds()->sum('amount'), 2)
            ]);
        }

        return redirect()->back()->with('success', 'Fund transfer record added successfully!');
    }

    public function socialAidDeleteFund(Request $request, $id, $fundId)
    {
        $user = auth()->user();
        $designationLower = strtolower($user->designation ?? '');
        $isSuperAdmin = ($user && ($user->isSuperAdmin() || $user->role == 1 || $user->role === 'super_admin'));
        $isCoo = ($user && ($user->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo')));
        $isHod = ($user && ($user->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod')));

        if (!$isSuperAdmin && !$isCoo && !$isHod) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized: Only HOD, COO, and Super Admin can delete financial records.'], 403);
            }
            return back()->with('error', 'Unauthorized Action: Only HOD, COO, and Super Admin can delete financial records.');
        }

        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }

        $fund = $project->funds()->findOrFail($fundId);
        $fund->delete();

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fund transfer record deleted successfully!',
                'total_amount' => number_format($project->funds()->sum('amount'), 2)
            ]);
        }

        return redirect()->back()->with('success', 'Fund transfer record deleted successfully!');
    }

    public function socialAidAddProgramme(Request $request, $id)
    {
        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }

        if ($request->isMethod('get')) {
            return redirect()->back();
        }

        $programmeName = $request->input('programme_name');
        if ($programmeName === 'Others' && $request->filled('other_programme_name')) {
            $programmeName = trim($request->input('other_programme_name'));
        }

        if ($request->input('programme_name') === 'Others') {
            $request->validate([
                'other_programme_name' => 'required|string|max:255',
                'date' => 'required|date',
                'place' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
            ]);
        } else {
            $request->validate([
                'programme_name' => 'required|string|max:255',
                'date' => 'required|date',
                'place' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
            ]);
        }

        // Prevent rapid duplicate submissions within 5 seconds
        $existingDuplicate = $project->programmes()
            ->where('programme_name', $programmeName)
            ->where('created_at', '>=', now()->subSeconds(5))
            ->first();

        if ($existingDuplicate) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Programme added successfully!',
                    'programme' => $existingDuplicate,
                    'formatted_date' => !empty($existingDuplicate->date) ? date('d-M-Y', strtotime($existingDuplicate->date)) : '-'
                ]);
            }
            return redirect()->back()->with('success', 'Programme added successfully!');
        }

        $fileKeys = ['photo', 'marklist', 'thanks_letter', 'report_form', 'medical_certificate', 'other_document'];
        $tickStatuses = [];
        foreach ($fileKeys as $fileKey) {
            $tickStatuses[$fileKey . '_ticked'] = $request->has($fileKey . '_ticked');
        }
        $tickStatuses['present_ticked'] = $request->has('present_ticked');

        $prog = $project->programmes()->create(array_merge([
            'programme_name' => $programmeName,
            'date' => $request->input('date'),
            'place' => $request->input('place'),
            'remarks' => $request->input('remarks'),
        ], $tickStatuses));

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Programme added successfully!',
                'programme' => $prog,
                'formatted_date' => !empty($prog->date) ? date('d-M-Y', strtotime($prog->date)) : '-'
            ]);
        }

        return redirect()->back()->with('success', 'Programme added successfully!');
    }

    public function socialAidUpdateProgramme(Request $request, $id, $programme_id)
    {
        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }

        $programme = $project->programmes()->findOrFail($programme_id);

        $programmeName = $request->input('programme_name');
        if ($programmeName === 'Others' && $request->filled('other_programme_name')) {
            $programmeName = trim($request->input('other_programme_name'));
        }

        if ($request->input('programme_name') === 'Others') {
            $request->validate([
                'other_programme_name' => 'required|string|max:255',
                'date' => 'required|date',
                'place' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
            ]);
        } else {
            $request->validate([
                'programme_name' => 'required|string|max:255',
                'date' => 'required|date',
                'place' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
            ]);
        }

        $fileKeys = ['photo', 'marklist', 'thanks_letter', 'report_form', 'medical_certificate', 'other_document'];
        $tickUpdates = [];

        foreach ($fileKeys as $fileKey) {
            $tickUpdates[$fileKey . '_ticked'] = $request->has($fileKey . '_ticked');
        }
        $tickUpdates['present_ticked'] = $request->has('present_ticked');

        $programme->update(array_merge([
            'programme_name' => $programmeName,
            'date' => $request->input('date'),
            'place' => $request->input('place'),
            'remarks' => $request->input('remarks'),
        ], $tickUpdates));

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Programme updated successfully!',
                'programme' => $programme->fresh(),
                'formatted_date' => !empty($programme->date) ? date('d-M-Y', strtotime($programme->date)) : '-'
            ]);
        }

        return redirect()->back()->with('success', 'Programme updated successfully!');
    }


    public function socialAidDeleteProgramme(Request $request, $id, $programme_id)
    {
        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            abort(404);
        }

        $programme = $project->programmes()->findOrFail($programme_id);
        $programme->delete();

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Programme deleted successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Programme deleted successfully!');
    }


    public function socialAidToggleProgrammeTick(Request $request, $id)
    {
        [$project, $application] = $this->getSocialAidProjectAndApp($id);
        if (!$project) {
            return response()->json(['success' => false, 'error' => 'Project not found.'], 404);
        }
        
        $programmeId = $request->input('programme_id');
        $field = $request->input('field');

        $programme = $project->programmes()->findOrFail($programmeId);

        $tickKey = str_ends_with($field, '_ticked') ? $field : ($field . '_ticked');
        $isTicked = !$programme->$tickKey;
        
        $programme->update([
            $tickKey => $isTicked,
        ]);

        return response()->json([
            'success' => true,
            'is_ticked' => $isTicked,
            'message' => $isTicked ? 'Item ticked successfully.' : 'Item unticked successfully.'
        ]);
    }

    public function orphanCareUploadPhoto(Request $request, $id) { return $this->socialAidUploadPhoto($request, $id); }
    public function orphanCareDeletePhoto(Request $request, $id) { return $this->socialAidDeletePhoto($request, $id); }
    public function orphanCareUpdateAddress(Request $request, $id) { return $this->socialAidUpdateAddress($request, $id); }
    public function orphanCareAddFund(Request $request, $id) { return $this->socialAidAddFund($request, $id); }
    public function orphanCareDeleteFund(Request $request, $id, $fundId) { return $this->socialAidDeleteFund($request, $id, $fundId); }
    public function orphanCareAddProgramme(Request $request, $id) { return $this->socialAidAddProgramme($request, $id); }
    public function orphanCareUpdateProgramme(Request $request, $id, $programme_id) { return $this->socialAidUpdateProgramme($request, $id, $programme_id); }
    public function orphanCareDeleteProgramme(Request $request, $id, $programme_id) { return $this->socialAidDeleteProgramme($request, $id, $programme_id); }
    public function orphanCareToggleProgrammeTick(Request $request, $id) { return $this->socialAidToggleProgrammeTick($request, $id); }

    /**
     * Compress and save uploaded image if size exceeds 2 MB (or has large dimensions).
     */
    private function compressAndSaveImage($file, $destinationPath, $maxSizeBytes = 2097152)
    {
        $dir = dirname($destinationPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $tempPath = $file->getRealPath();
        $fileSize = filesize($tempPath);

        // If file is already <= 2 MB, move directly for instantaneous speed (under 10ms)
        if ($fileSize <= $maxSizeBytes) {
            return $file->move($dir, basename($destinationPath));
        }

        $imageInfo = @getimagesize($tempPath);
        if (!$imageInfo) {
            return $file->move($dir, basename($destinationPath));
        }

        $mime = $imageInfo['mime'] ?? '';
        $width = $imageInfo[0] ?? 0;
        $height = $imageInfo[1] ?? 0;

        $srcImage = null;
        switch ($mime) {
            case 'image/jpeg':
                $srcImage = @imagecreatefromjpeg($tempPath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($tempPath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($tempPath);
                break;
            case 'image/avif':
                $srcImage = function_exists('imagecreatefromavif') ? @imagecreatefromavif($tempPath) : null;
                break;
            case 'image/gif':
                $srcImage = @imagecreatefromgif($tempPath);
                break;
        }

        if (!$srcImage) {
            return $file->move($dir, basename($destinationPath));
        }

        $maxWidth = 1600;
        $maxHeight = 1600;
        $newWidth = $width;
        $newHeight = $height;

        if ($newWidth > $maxWidth || $newHeight > $maxHeight) {
            $ratio = min($maxWidth / $newWidth, $maxHeight / $newHeight);
            $newWidth = (int)round($newWidth * $ratio);
            $newHeight = (int)round($newHeight * $ratio);
        }

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if ($mime === 'image/png') {
            @imagepng($destImage, $destinationPath, 3);
        } elseif ($mime === 'image/webp') {
            @imagewebp($destImage, $destinationPath, 75);
        } elseif ($mime === 'image/avif' && function_exists('imageavif')) {
            @imageavif($destImage, $destinationPath, 70);
        } else {
            @imagejpeg($destImage, $destinationPath, 75);
        }

        imagedestroy($srcImage);
        imagedestroy($destImage);

        return true;
    }

    public function socialAidToggleSuspend(Request $request, $id)
    {
        $user = auth()->user();
        $isAjax = $request->ajax()
            || $request->wantsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest'
            || str_contains($request->header('Accept', ''), 'application/json');

        if (!$user || (!$user->isSuperAdmin() && !$user->isCoo() && !$user->isHod() && !$user->isPm() && !$user->isSocialAid())) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to update project status.'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized to update project status.');
        }

        $typeMap = [
            \App\Models\OrphanCareProject::class       => ['slug' => 'orphan-care',       'category' => 'Orphan Care'],
            \App\Models\DifferentlyAbledProject::class => ['slug' => 'differently-abled', 'category' => 'Differently Abled'],
            \App\Models\FamilyAidProject::class        => ['slug' => 'family-aid',        'category' => 'Family Aid'],
        ];

        // Determine which model based on the route slug
        $routeSlug = null;
        $currentPath = $request->path(); // e.g. "admin/projects/orphan-care/8/toggle-suspend"
        foreach ($typeMap as $modelClass => $info) {
            if (str_contains($currentPath, $info['slug'])) {
                $project = $modelClass::find($id);
                $routeSlug = $info['slug'];
                break;
            }
        }

        // Fallback: search all models
        if (empty($routeSlug)) {
            $project = null;
            foreach ($typeMap as $modelClass => $info) {
                $found = $modelClass::find($id);
                if ($found) {
                    $project = $found;
                    $routeSlug = $info['slug'];
                    break;
                }
            }
        }

        if (!isset($project) || !$project) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Project not found.'], 404);
            }
            abort(404);
        }

        if ($project->status === 'Suspended') {
            $project->status = 'Active';
            $message = 'Project has been reactivated successfully.';
        } else {
            $project->status = 'Suspended';
            $message = 'Project has been suspended successfully.';
        }
        $project->save();

        try {
            \App\Events\ProjectUpdated::dispatch($project->id, $routeSlug, auth()->id(), 'status_toggled', [
                'status'  => $project->status,
                'message' => $message
            ]);
        } catch (\Exception $e) {}

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status'  => $project->status,
            ]);
        }

        $redirectUrl = $request->input('redirect_back') ?: route('projects.category', $routeSlug);
        return redirect($redirectUrl)->with('success', $message);
    }
}

