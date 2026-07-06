<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Http\Request;

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

        $projects = $model::with(['donor', 'projectManager'])->orderBy('created_at', 'desc')->get();

        $donors = Donor::all();
        $managers = User::where('role', 3)
            ->orWhere('designation', 'like', '%project manager%')
            ->get();

        return view($config['view'], compact(
            'categoryName',
            'categorySlug',
            'projects',
            'donors',
            'managers'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agency_project_no' => ['required', 'string', 'max:255'],
            'donor_id' => ['required', 'exists:donors,id'],
            'project_manager_id' => ['required', 'exists:users,id'],
            'available_budget' => ['required', 'numeric', 'min:0'],
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
            $model::create($data);
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
            'available_budget' => ['required', 'numeric', 'min:0'],
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
            $project->update($data);
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

    public function show(Request $request, $id)
    {
        $project = null;
        $type = $request->query('type');
        if ($type && array_key_exists($type, $this->categories)) {
            $model = $this->categories[$type]['model'];
            $project = $model::with(['donor', 'projectManager'])->find($id);
        }

        if (!$project) {
            foreach ($this->categories as $slug => $config) {
                $project = $config['model']::with(['donor', 'projectManager'])->find($id);
                if ($project) {
                    break;
                }
            }
        }

        if (!$project) {
            abort(404);
        }

        $appModel = str_replace('Project', 'Application', get_class($project));
        $application = null;
        $allApplications = [];
        if (class_exists($appModel)) {
            $allApplications = $appModel::orderBy('created_at', 'desc')->get();
            if ($project->application_id) {
                $application = $appModel::find($project->application_id);
            }
            if (!$application && $project->type_of_project !== 'Education Center') {
                $application = $appModel::find($project->id) ?? $appModel::first();
            }
        }

        return view('admin.project_detail', compact('project', 'application', 'allApplications'));
    }

    public function assignApplication(Request $request, $id)
    {
        $user = auth()->user();
        
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

        $isCoo = ($user->role === 2 || strtolower($user->designation) === 'coo');
        $isPm = ($user->role === 3 || strtolower($user->designation) === 'project manager');

        if ($project->type_of_project === 'Education Center') {
            if (!$isCoo && !$isPm) {
                return redirect()->back()->with('error', 'Only Project Manager and COO are authorized to assign applications.');
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

        // Education Center project stage flow implementation
        if ($project->type_of_project === 'Education Center') {
            $currentStage = $project->stage;

            if ($currentStage == 1) {
                if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
                    return redirect()->back()->with('error', 'Only COO is authorized to verify Stage 1.');
                }
                $project->stage = 2;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 1 verified successfully by COO! Project promoted to Stage 2.');
            }

            if ($currentStage == 2) {
                $isCoo = ($user->role === 2 || strtolower($user->designation) === 'coo');
                $isHod = ($user->role === 4 || strtolower($user->designation) === 'hod');
                if (!$isCoo && !$isHod) {
                    return redirect()->back()->with('error', 'Only COO or HOD is authorized to approve Stage 2.');
                }
                if (empty($project->application_id)) {
                    return redirect()->back()->with('error', 'Please connect an application first before approving Stage 2.');
                }
                $project->stage = 3;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 2 approved successfully! Project promoted to Stage 3.');
            }

            if ($currentStage == 3) {
                if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
                    return redirect()->back()->with('error', 'Only COO is authorized to approve Stage 3.');
                }
                $project->stage = 4;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 3 approved successfully! Project promoted to Stage 4.');
            }

            if ($currentStage == 4) {
                if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
                    return redirect()->back()->with('error', 'Only COO is authorized to approve Stage 4.');
                }
                $project->stage = 5;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 4 approved successfully! Project promoted to Stage 5.');
            }

            if ($currentStage == 5) {
                if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
                    return redirect()->back()->with('error', 'Only COO is authorized to approve Stage 5.');
                }
                $project->stage = 6;
                $project->save();
                return redirect()->route('projects.show', $project->id)->with('success', 'Stage 5 approved successfully! Project promoted to Stage 6.');
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
        if ($user->role !== 3 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to add files.');
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
            'document_name' => 'required|string',
            'file' => 'required|file|max:10240' // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            
            // Save file in public/uploads/projects/{project_id}/
            $docNameClean = str_replace(' ', '_', strtolower($request->input('document_name')));
            $filename = $docNameClean . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
            
            $uploadedFile->move(public_path('uploads/projects/' . $project->id), $filename);
            
            $files = $project->files ?? [];
            $files[$request->input('document_name')] = 'uploads/projects/' . $project->id . '/' . $filename;
            
            $project->files = $files;
            $project->save();

            return redirect()->route('projects.show', $id)->with('success', $request->input('document_name') . ' uploaded successfully!');
        }

        return redirect()->back()->with('error', 'File upload failed.');
    }

    public function addMaterial(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage materials.');
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
            $materials = [
                ['material' => 'cement', 'amount' => 8000],
                ['material' => 'metal', 'amount' => 8000]
            ];
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
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage materials.');
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
            $materials = [
                ['material' => 'cement', 'amount' => 8000],
                ['material' => 'metal', 'amount' => 8000]
            ];
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
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage materials.');
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
            $materials = [
                ['material' => 'cement', 'amount' => 8000],
                ['material' => 'metal', 'amount' => 8000]
            ];
        }

        if (isset($materials[$index])) {
            array_splice($materials, $index, 1);
            $project->materials = $materials;
            $project->save();
            return redirect()->route('projects.show', $id)->with('success', 'Material deleted successfully!');
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    public function addExpense(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage expenses.');
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
            'material_index' => 'required|integer',
            'expense_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $expenses = $project->expenses;
        if (empty($expenses)) {
            $expenses = [];
        }

        $expenses[] = [
            'material_index' => (int)$request->input('material_index'),
            'expense_name' => $request->input('expense_name'),
            'amount' => (float)$request->input('amount')
        ];

        $project->expenses = $expenses;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Expense added successfully!');
    }

    public function updateExpense(Request $request, $id, $index)
    {
        $user = auth()->user();
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage expenses.');
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
            'material_index' => 'required|integer',
            'expense_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $expenses = $project->expenses;
        if (empty($expenses)) {
            $expenses = [];
        }

        if (isset($expenses[$index])) {
            $expenses[$index] = [
                'material_index' => (int)$request->input('material_index'),
                'expense_name' => $request->input('expense_name'),
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
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage expenses.');
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
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to add photos.');
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
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage photos.');
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
        if ($user->role !== 3 && $user->role !== 1 && strtolower($user->designation) !== 'project manager') {
            return redirect()->back()->with('error', 'Only Project Manager is authorized to manage completion details.');
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
            'geo_location' => 'nullable|string|max:255'
        ]);

        $files = $project->files ?? [];
        $files['completion_details'] = [
            'total_amount' => (float)$request->input('total_amount'),
            'amount_paid_by_donor' => (float)$request->input('amount_paid_by_donor'),
            'community_contribution' => (float)$request->input('community_contribution'),
            'any_other' => (float)$request->input('any_other'),
            'geo_location' => $request->input('geo_location')
        ];
        
        $project->files = $files;
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Completion details saved successfully!');
    }
}
