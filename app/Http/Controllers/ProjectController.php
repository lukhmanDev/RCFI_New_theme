<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\User;
use App\Models\Contractor;
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
        $counts = [];
        foreach ($this->categories as $slug => $config) {
            $model = $config['model'];
            $counts[$config['name']] = $model::count();
        }

        return view('admin.projects_dashboard', [
            'groupedCategories' => $this->groupedCategories,
            'counts' => $counts
        ]);
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

        $projects = $model::with(['donor', 'projectManager', 'engineer'])->orderBy('created_at', 'desc')->get();

        $donors = Donor::all();
        $managers = User::where('role', 3)
            ->orWhere('designation', 'like', '%project manager%')
            ->get();

        $engineers = User::where('role', 6)
            ->orWhere('designation', 'like', '%engineer%')
            ->get();

        return view($config['view'], compact(
            'categoryName',
            'categorySlug',
            'projects',
            'donors',
            'managers',
            'engineers'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agency_project_no' => ['required', 'string', 'max:255'],
            'donor_id' => ['required', 'exists:donors,id'],
            'project_manager_id' => ['required', 'exists:users,id'],
            'engineer_id' => ['nullable', 'exists:users,id'],
            'unit' => ['nullable', 'string', 'max:255'],
            'available_budget' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'type_of_project' => ['required', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);

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
            if (in_array($data['type_of_project'], ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House'])) {
                $data['stage'] = 1;
            } else {
                $data['stage'] = 6;
            }
            try {
                $model::create($data);
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
        if (!in_array($user->role, [1, 2, 4])) {
            return redirect()->back()->with('error', 'You are not authorized to edit projects.');
        }

        $data = $request->validate([
            'agency_project_no' => ['required', 'string', 'max:255'],
            'donor_id' => ['required', 'exists:donors,id'],
            'project_manager_id' => ['required', 'exists:users,id'],
            'engineer_id' => ['nullable', 'exists:users,id'],
            'unit' => ['nullable', 'string', 'max:255'],
            'available_budget' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'type_of_project' => ['required', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);

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
        if (!in_array($user->role, [1, 2, 4])) {
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

    private function getProjectInstance(Request $request, $id)
    {
        $this->resolveActiveCategory($request);
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
            }
        }
        
        // 2. Fallback to Session
        if (!$type) {
            $type = session('active_project_type_' . $id);
        }

        // 3. Resolve model if type is found
        if ($type) {
            foreach ($this->categories as $slug => $config) {
                if (strtolower($config['name']) === strtolower($type) || strtolower($slug) === strtolower($type)) {
                    $model = $config['model'];
                    $project = $model::find($id);
                    if ($project) {
                        session(['active_project_type_' . $id => $config['name']]);
                        return $project;
                    }
                }
            }
        }

        // 4. Ultimate fallback: loop through all models
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                session(['active_project_type_' . $id => $config['name']]);
                return $project;
            }
        }

        return null;
    }

    public function show(Request $request, $id)
    {
        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            abort(404);
        }

        $project->load(['donor', 'projectManager', 'engineer']);

        $appModel = str_replace('Project', 'Application', get_class($project));
        $application = null;
        $allApplications = [];
        if (class_exists($appModel)) {
            $allApplications = $appModel::where('status', 'Approved')->orderBy('created_at', 'desc')->get();
            if ($project->application_id) {
                $application = $appModel::find($project->application_id);
            }
            if (!$application && !in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House'])) {
                $application = $appModel::find($project->id) ?? $appModel::first();
            }
        }

        $allContractors = Contractor::orderBy('name')->get();
        return view('admin.project_detail', compact('project', 'application', 'allApplications', 'allContractors'));
    }

    public function assignApplication(Request $request, $id)
    {
        $user = auth()->user();
        
        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            abort(404);
        }

        $isCoo = ($user->role === 2 || strtolower($user->designation) === 'coo');
        $isPm = ($user->role === 3 || strtolower($user->designation) === 'project manager');
        $isEngineer = ($user->role === 6 || strtolower($user->designation) === 'engineer');

        if (in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House'])) {
            if ($project->type_of_project === 'Education Center') {
                if (!$isCoo && !$isPm) {
                    return redirect()->back()->with('error', 'Only Project Manager and COO are authorized to assign applications.');
                }
            } else {
                if (!$isCoo && !$isPm && !$isEngineer) {
                    return redirect()->back()->with('error', 'Only Project Manager, COO and Engineer are authorized to assign applications.');
                }
            }
        } else {
            if (!$isCoo) {
                return redirect()->back()->with('error', 'Only COO is authorized to assign applications.');
            }
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
        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            abort(404);
        }

        // Education Center project stage flow implementation
        if (in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House'])) {
            $currentStage = $project->stage;

            if ($currentStage == 1) {
                $project->stage = 2;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 1 verified successfully! Project promoted to Stage 2.');
            }

            if ($currentStage == 2) {
                if (empty($project->application_id)) {
                    return redirect()->back()->with('error', 'Please connect an application first before approving Stage 2.');
                }
                $project->stage = 3;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 2 approved successfully! Project promoted to Stage 3.');
            }

            if ($currentStage == 3) {
                $project->stage = 4;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 3 approved successfully! Project promoted to Stage 4.');
            }

            if ($currentStage == 4) {
                if ($project->type_of_project === 'Education Center') {
                    if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
                        return redirect()->back()->with('error', 'Only COO is authorized to approve Stage 4.');
                    }
                }
                $project->stage = 5;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 4 approved successfully! Project promoted to Stage 5.');
            }

            if ($currentStage == 5) {
                $project->stage = 6;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Project promoted to Stage 6 successfully.');
            }

            if ($currentStage == 6) {
                if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
                    return redirect()->back()->with('error', 'Only COO is authorized to finalize the project.');
                }
                $project->status = 'Approved';
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Project completely approved and finalized by COO!');
            }

            return redirect()->back()->with('error', 'Invalid stage progression.');
        }

        // Standard approval for other projects
        if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
            return redirect()->back()->with('error', 'Only COO is authorized to approve projects.');
        }
        
        $project->status = 'Approved';
        $project->stage = 6;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Project completely approved and finalized by COO!');
    }

    public function export($category)
    {
        if (!array_key_exists($category, $this->categories)) {
            abort(404);
        }

        $config = $this->categories[$category];
        $model = $config['model'];
        $projects = $model::with(['donor', 'projectManager'])->get();

        $headers = [
            'Project ID',
            'Agency Project No',
            'Donor Name',
            'Project Manager',
            'Available Budget',
            'Type of Project',
            'Remarks',
            'Stage',
            'Status',
            'Created At'
        ];

        $callback = function() use ($projects, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->project_id,
                    $project->agency_project_no ?? 'N/A',
                    $project->donor ? $project->donor->name : 'N/A',
                    $project->projectManager ? $project->projectManager->name : 'N/A',
                    $project->available_budget,
                    $project->type_of_project,
                    $project->remarks ?? 'N/A',
                    'Stage ' . $project->stage,
                    $project->status,
                    $project->created_at
                ]);
            }

            fclose($file);
        };

        $filename = str_replace(' ', '_', strtolower($config['name'])) . '_projects_' . date('Ymd_His') . '.csv';

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function uploadFile(Request $request, $id)
    {
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to add files.');
        }

        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            abort(404);
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Only Project Manager and Engineer are authorized to toggle checklist.'], 403);
            }
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to toggle checklist.');
        }

        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Project not found.'], 404);
            }
            abort(404);
        }

        if (empty($project->application_id)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please connect an application first.'], 400);
            }
            return redirect()->back()->with('error', 'Please connect an application first.');
        }

        $request->validate([
            'document_name' => 'required|string',
        ]);

        $docName = $request->input('document_name');
        $column = \App\Models\ProjectDocument::$docColumnMap[$docName] ?? null;
        if (!$column) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Invalid document name.'], 400);
            }
            return redirect()->back()->with('error', 'Invalid document name.');
        }

        $docRecord = $project->projectDocument;
        if (!$docRecord) {
            $docRecord = $project->projectDocument()->create();
        }

        $currentVal = $docRecord->$column;
        $ticked = false;
        $tickedAtStr = '';

        if ($currentVal && $currentVal !== '0') {
            // Untick: delete physical file if custom file uploaded
            if ($currentVal !== '1') {
                $physicalPath = public_path($currentVal);
                if (file_exists($physicalPath) && is_file($physicalPath)) {
                    unlink($physicalPath);
                }
            }
            $docRecord->$column = '0';
            $timeColumn = $column . '_ticked_at';
            $docRecord->$timeColumn = null;
            $ticked = false;
            $msg = "$docName removed successfully.";
        } else {
            // Tick: set value to 1 and update ticked_at timestamp
            $docRecord->$column = '1';
            $timeColumn = $column . '_ticked_at';
            $now = now();
            $docRecord->$timeColumn = $now;
            $ticked = true;
            $tickedAtStr = $now->timezone('Asia/Kolkata')->format('d-M-Y h:i A');
            $msg = "$docName ticked.";
        }

        $docRecord->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => $msg, 
                'ticked' => $ticked,
                'ticked_at' => $tickedAtStr
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function updatePhase(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 3 && $user->role !== 1 && $user->role !== 2 && strtolower($user->designation) !== 'project manager' && strtolower($user->designation) !== 'coo') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Only Project Manager and COO are authorized to update project status.'], 403);
            }
            return redirect()->back()->with('error', 'Only Project Manager and COO are authorized to update project status.');
        }

        $request->validate([
            'project_phase'        => 'required|string',
            'project_phase_custom' => 'nullable|string|max:255',
        ]);

        $project = $this->getProjectInstance($request, $id);
        if (!$project) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Project not found.'], 404);
            }
            abort(404);
        }

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

        $updatedAt = $statusRecord->updated_at ? $statusRecord->updated_at->timezone('Asia/Kolkata') : now();

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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage materials.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        if (empty($project->application_id)) {
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage materials.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage materials.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage community contributions.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        $request->validate([
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $files = $project->files ?? [];
        $commContribs = $files['community_contributions'] ?? [];
        if (empty($commContribs)) {
            $compDetails = $files['completion_details'] ?? [];
            $commContribs = [
                ['item' => 'Community Contribution', 'amount' => $compDetails['community_contribution'] ?? 0],
                ['item' => 'Other', 'amount' => $compDetails['any_other'] ?? 0]
            ];
        }

        $commContribs[] = [
            'item' => $request->input('item'),
            'amount' => (float)$request->input('amount')
        ];

        $files['community_contributions'] = $commContribs;
        
        // Synced compatibility values for completion_details
        $compDetails = $files['completion_details'] ?? [];
        $compDetails['community_contribution'] = 0.0;
        $compDetails['any_other'] = 0.0;
        foreach ($commContribs as $c) {
            if ($c['item'] === 'Community Contribution') {
                $compDetails['community_contribution'] += $c['amount'];
            } else if ($c['item'] === 'Other') {
                $compDetails['any_other'] += $c['amount'];
            } else {
                // If it is any other row, let's add it to community_contribution or other? Let's add it to community_contribution by default or keep completion_details updated
                $compDetails['community_contribution'] += $c['amount'];
            }
        }
        $files['completion_details'] = $compDetails;

        $project->files = $files;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Community contribution added successfully!');
    }

    public function updateCommunityContribution(Request $request, $id, $index)
    {
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage community contributions.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        $request->validate([
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $files = $project->files ?? [];
        $commContribs = $files['community_contributions'] ?? [];
        if (empty($commContribs)) {
            $compDetails = $files['completion_details'] ?? [];
            $commContribs = [
                ['item' => 'Community Contribution', 'amount' => $compDetails['community_contribution'] ?? 0],
                ['item' => 'Other', 'amount' => $compDetails['any_other'] ?? 0]
            ];
        }

        if (isset($commContribs[$index])) {
            $commContribs[$index] = [
                'item' => $request->input('item'),
                'amount' => (float)$request->input('amount')
            ];
            
            // Synced compatibility values for completion_details
            $compDetails = $files['completion_details'] ?? [];
            $compDetails['community_contribution'] = 0.0;
            $compDetails['any_other'] = 0.0;
            foreach ($commContribs as $c) {
                if ($c['item'] === 'Community Contribution') {
                    $compDetails['community_contribution'] += $c['amount'];
                } else if ($c['item'] === 'Other') {
                    $compDetails['any_other'] += $c['amount'];
                } else {
                    $compDetails['community_contribution'] += $c['amount'];
                }
            }
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage community contributions.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        $files = $project->files ?? [];
        $commContribs = $files['community_contributions'] ?? [];
        if (empty($commContribs)) {
            $compDetails = $files['completion_details'] ?? [];
            $commContribs = [
                ['item' => 'Community Contribution', 'amount' => $compDetails['community_contribution'] ?? 0],
                ['item' => 'Other', 'amount' => $compDetails['any_other'] ?? 0]
            ];
        }

        if (isset($commContribs[$index])) {
            array_splice($commContribs, $index, 1);
            
            // Synced compatibility values for completion_details
            $compDetails = $files['completion_details'] ?? [];
            $compDetails['community_contribution'] = 0.0;
            $compDetails['any_other'] = 0.0;
            foreach ($commContribs as $c) {
                if ($c['item'] === 'Community Contribution') {
                    $compDetails['community_contribution'] += $c['amount'];
                } else if ($c['item'] === 'Other') {
                    $compDetails['any_other'] += $c['amount'];
                } else {
                    $compDetails['community_contribution'] += $c['amount'];
                }
            }
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage expenses.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        if (empty($project->application_id)) {
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage expenses.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage expenses.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to add photos.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        $request->validate([
            'photo' => 'required|image|max:10240' // 10MB max
        ]);

        if ($request->hasFile('photo')) {
            $uploadedFile = $request->file('photo');
            $filename = 'photo_' . time() . '_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
            $uploadedFile->move(public_path('uploads/projects/' . $project->id), $filename);
            
            $files = $project->files ?? [];
            $photos = $files['photos'] ?? [];
            $photos[] = 'uploads/projects/' . $project->id . '/' . $filename;
            $files['photos'] = $photos;
            
            $project->files = $files;
            $project->save();

            return redirect()->route('projects.show', $id)->with('success', 'Photo uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Photo upload failed.');
    }

    public function deletePhoto(Request $request, $id, $index)
    {
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage photos.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        $files = $project->files ?? [];
        $photos = $files['photos'] ?? [];

        if (isset($photos[$index])) {
            $filepath = public_path($photos[$index]);
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            array_splice($photos, $index, 1);
            $files['photos'] = $photos;
            $project->files = $files;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Photo deleted successfully!');
        }

        return redirect()->back()->with('error', 'Photo not found.');
    }

    public function saveCompletionDetails(Request $request, $id)
    {
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage completion details.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'amount_paid_by_donor' => 'required|numeric|min:0',
            'community_contribution' => 'required|numeric|min:0',
            'any_other' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0'
        ]);

        $files = $project->files ?? [];
        $files['completion_details'] = [
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

        return redirect()->route('projects.show', $id)->with('success', 'Completion details saved successfully!');
    }

    public function addContractor(Request $request, $id)
    {
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage contractor details.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
        }

        if (empty($project->application_id)) {
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage contractor details.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
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
        $user = auth()->user();
        if (!$this->isPmOrEngineer($user)) {
            return redirect()->back()->with('error', 'Only Project Manager and Engineer are authorized to manage contractor details.');
        }

        $project = null;
        foreach ($this->categories as $slug => $config) {
            $project = $config['model']::find($id);
            if ($project) {
                break;
            }
        }

        if (!$project) {
            abort(404);
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

    private function isPmOrEngineer($user)
    {
        if (!$user) {
            return false;
        }
        return ($user->role === 3 || $user->role === 1 || $user->role === 6 || strtolower($user->designation) === 'project manager' || strtolower($user->designation) === 'engineer');
    }
}
