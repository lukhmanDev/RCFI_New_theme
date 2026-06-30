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
            'model' => \App\Models\EducationCenterProject::class
        ],
        'cultural-center' => [
            'name' => 'Cultural Center',
            'model' => \App\Models\CulturalCenterProject::class
        ],
        'hospital-or-clinics' => [
            'name' => 'Hospital or Clinics',
            'model' => \App\Models\HospitalClinicProject::class
        ],
        'shops-and-others' => [
            'name' => 'Shops and Others',
            'model' => \App\Models\ShopOtherProject::class
        ],
        'house' => [
            'name' => 'House',
            'model' => \App\Models\HouseProject::class
        ],
        'drinking-water-group-level' => [
            'name' => 'Drinking Water - Group Level',
            'model' => \App\Models\DrinkingWaterGroupProject::class
        ],
        'drinking-water-individual-level' => [
            'name' => 'Drinking Water - Individual Level',
            'model' => \App\Models\DrinkingWaterIndividualProject::class
        ],
        'orphan-care' => [
            'name' => 'Orphan Care',
            'model' => \App\Models\OrphanCareProject::class
        ],
        'differently-abled' => [
            'name' => 'Differently Abled',
            'model' => \App\Models\DifferentlyAbledProject::class
        ],
        'family-aid' => [
            'name' => 'Family Aid',
            'model' => \App\Models\FamilyAidProject::class
        ],
        'general' => [
            'name' => 'General',
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

        return view('admin.projects_list', [
            'categoryName' => $categoryName,
            'categorySlug' => $categorySlug,
            'projects' => $projects,
            'donors' => $donors,
            'managers' => $managers
        ]);
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
            if (!$application) {
                $application = $appModel::find($project->id) ?? $appModel::first();
            }
        }

        return view('admin.project_detail', compact('project', 'application', 'allApplications'));
    }

    public function assignApplication(Request $request, $id)
    {
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
            'application_id' => 'required'
        ]);

        $project->application_id = $request->input('application_id');
        $project->save();

        return redirect()->route('projects.show', $id)->with('success', 'Application connected to this project successfully!');
    }

    public function approveStage(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 2 && strtolower($user->designation) !== 'coo') {
            return redirect()->back()->with('error', 'Only COO is authorized to approve projects.');
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
}
