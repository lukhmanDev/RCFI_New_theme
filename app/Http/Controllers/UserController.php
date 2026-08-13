<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->user() || !auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action. Only administrators can access User Management.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $authUser = auth()->user();
        $users = User::nonSuperAdmin()->where('id', '!=', $authUser->id)->forHod($authUser)->orderBy('created_at', 'desc')->get();

        $today = now()->format('Y-m-d');
        $onLeaveCount = User::forHod($authUser)->whereHas('leaveRequests', function($q) use ($today) {
            $q->where('status', 'Approved')
              ->where('start_date', '<=', $today)
              ->where('end_date', '>=', $today);
        })->count();

        $hods = User::where(function($q) {
            $q->whereIn('role', ['hod', '4', 'HOD'])->orWhere('is_hr', true);
        })->where('is_suspended', false)->orderBy('name', 'asc')->get();

        return view('admin.users', compact('users', 'onLeaveCount', 'hods'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('users')->withErrors(['Only Super Admin can add new staff members.']);
        }
        
        $rules = [
            'name'           => ['required', 'string', 'min:2', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile'         => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'father_name'    => ['required', 'string', 'max:255'],
            'mother_name'    => ['required', 'string', 'max:255'],
            'date_of_birth'  => ['required', 'date'],
            'date_of_joining'=> ['required', 'date'],
            'gender'         => ['required', 'string', 'in:Male,Female,Other'],
            'marital_status' => ['required', 'string', 'in:Single,Married,Divorced,Widowed'],
            'house_name'     => ['required', 'string', 'max:255'],
            'place'          => ['required', 'string', 'max:255'],
            'po'             => ['required', 'string', 'max:255'],
            'district'       => ['required', 'string', 'max:255'],
            'state'          => ['required', 'string', 'max:255'],
            'pin_code'       => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'aadhar_number'  => ['required', 'string', 'max:20'],
            'pan_card_number'=> ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'max:30'],
            'bank_name'      => ['required', 'string', 'max:255'],
            'bank_branch'    => ['required', 'string', 'max:255'],
            'ifsc_code'      => ['required', 'string', 'max:20'],
            'designation'    => ['required', 'string', 'max:255'],
            'password'       => ['required', 'string', 'min:8'],
            'hod_id'          => ['nullable', 'exists:users,id'],
            'assigned_hod_id' => ['nullable', 'exists:users,id'],
            'is_hr'           => ['nullable', 'boolean'],
            'Is_hr'           => ['nullable', 'boolean'],
        ];

        if (auth()->user()->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', 'in:super_admin,coo,project_manager,hod,others,engineer,reception,social_aid,employee,Super Admin,COO,Project Manager,HOD,Others,Engineer,Reception,Social Aid,Social Aid Manager,Employee,1,2,3,4,5,6,7,8,9'];
        }

        $messages = [
            'mobile.regex'   => 'Mobile number must be exactly 10 digits.',
            'pin_code.regex' => 'PIN code must be exactly 6 digits.',
        ];

        $data = $request->validate($rules, $messages);

        if (!auth()->user()->isSuperAdmin()) {
            $data['role'] = 'others'; // default to 'others'
        }

        $tempUser = new User();
        $tempUser->role = $data['role'] ?? 'others';
        $normalizedRole = $tempUser->role;
        $excludedRoles = ['super_admin', 'coo', 'hod'];

        $hodId = $request->input('hod_id') ?: $request->input('assigned_hod_id');

        if (!in_array($normalizedRole, $excludedRoles)) {
            if (empty($hodId)) {
                return redirect()->back()->withInput()->withErrors(['assigned_hod_id' => 'Please assign an HOD before creating this staff member.']);
            }
            $data['hod_id'] = $hodId;
            $data['assigned_hod_id'] = $hodId;
        } else {
            $data['hod_id'] = null;
            $data['assigned_hod_id'] = null;
        }

        $data['is_hr'] = $request->boolean('is_hr') || $request->boolean('Is_hr');

        if (!empty($data['pan_card_number'])) {
            $data['pan_card_number'] = strtoupper($data['pan_card_number']);
        }
        if (!empty($data['ifsc_code'])) {
            $data['ifsc_code'] = strtoupper($data['ifsc_code']);
        }

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return redirect()->route('users')->with('success', 'User registered successfully!');
    }

    public function update(Request $request, $id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);

        // Prevent editing yourself
        if (auth()->id() == $user->id) {
            return redirect()->route('users')->withErrors(['You cannot edit your own logged-in account.']);
        }

        $rules = [
            'name'           => ['required', 'string', 'min:2', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile'         => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'father_name'    => ['required', 'string', 'max:255'],
            'mother_name'    => ['required', 'string', 'max:255'],
            'date_of_birth'  => ['required', 'date'],
            'date_of_joining'=> ['required', 'date'],
            'gender'         => ['required', 'string', 'in:Male,Female,Other'],
            'marital_status' => ['required', 'string', 'in:Single,Married,Divorced,Widowed'],
            'house_name'     => ['required', 'string', 'max:255'],
            'place'          => ['required', 'string', 'max:255'],
            'po'             => ['required', 'string', 'max:255'],
            'district'       => ['required', 'string', 'max:255'],
            'state'          => ['required', 'string', 'max:255'],
            'pin_code'       => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'aadhar_number'  => ['required', 'string', 'max:20'],
            'pan_card_number'=> ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'max:30'],
            'bank_name'      => ['required', 'string', 'max:255'],
            'bank_branch'    => ['required', 'string', 'max:255'],
            'ifsc_code'      => ['required', 'string', 'max:20'],
            'designation'    => ['required', 'string', 'max:255'],
            'password'       => ['nullable', 'string', 'min:8'],
            'hod_id'          => ['nullable', 'exists:users,id'],
            'assigned_hod_id' => ['nullable', 'exists:users,id'],
            'is_hr'           => ['nullable', 'boolean'],
            'Is_hr'           => ['nullable', 'boolean'],
        ];

        if (auth()->user()->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', 'in:super_admin,coo,project_manager,hod,others,engineer,reception,social_aid,employee,Super Admin,COO,Project Manager,HOD,Others,Engineer,Reception,Social Aid,Social Aid Manager,Employee,1,2,3,4,5,6,7,8,9'];
        }

        $messages = [
            'mobile.regex'   => 'Mobile number must be exactly 10 digits.',
            'pin_code.regex' => 'PIN code must be exactly 6 digits.',
        ];

        $data = $request->validate($rules, $messages);

        if (!auth()->user()->isSuperAdmin()) {
            $data['role'] = $user->role;
            $data['designation'] = $user->designation;
        }

        $tempUser = new User();
        $tempUser->role = $data['role'] ?? $user->role;
        $normalizedRole = $tempUser->role;
        $excludedRoles = ['super_admin', 'coo', 'hod'];

        $hodId = $request->input('hod_id') ?: $request->input('assigned_hod_id');

        if (!in_array($normalizedRole, $excludedRoles)) {
            if (empty($hodId)) {
                return redirect()->back()->withInput()->withErrors(['assigned_hod_id' => 'Please assign an HOD before saving this staff member.']);
            }
            $data['hod_id'] = $hodId;
            $data['assigned_hod_id'] = $hodId;
        } else {
            $data['hod_id'] = null;
            $data['assigned_hod_id'] = null;
        }

        $data['is_hr'] = $request->boolean('is_hr') || $request->boolean('Is_hr');

        if (!empty($data['pan_card_number'])) {
            $data['pan_card_number'] = strtoupper($data['pan_card_number']);
        }
        if (!empty($data['ifsc_code'])) {
            $data['ifsc_code'] = strtoupper($data['ifsc_code']);
        }

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users')->with('success', 'User details updated successfully!');
    }

    public function destroy($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if (auth()->id() == $user->id) {
            return redirect()->route('users')->withErrors(['You cannot delete your own logged-in account.']);
        }

        $user->delete();

        return redirect()->route('users')->with('success', 'User account deleted successfully.');
    }

    public function getDetails($id)
    {
        $this->checkAdmin();
        $user = User::with('profile')->findOrFail($id);
        
        $designationLower = strtolower($user->designation ?? '');
        $isPmOrEngineer = $user->isPm() || $user->isEngineer() || in_array($designationLower, ['project manager', 'engineer', 'pm', 'site engineer', 'project engineer']);

        $projects = [];
        if ($isPmOrEngineer) {
            $projects = $user->assigned_projects->map(function ($project) use ($user) {
                $pmId = $project->project_manager_id ?? null;
                $engId = $project->engineer_id ?? null;

                $projectRole = 'Unknown';
                if ($pmId == $user->id && $engId == $user->id) {
                    $projectRole = 'PM & Engineer';
                } elseif ($pmId == $user->id) {
                    $projectRole = 'Project Manager';
                } elseif ($engId == $user->id) {
                    $projectRole = 'Engineer';
                }

                return [
                    'project_id' => $project->project_id ?? 'N/A',
                    'title' => $project->name ?? $project->type_of_project ?? 'Untitled Project',
                    'type' => $project->type_of_project,
                    'role' => $projectRole,
                    'status' => $project->status ?? 'Pending',
                ];
            });
        }

        $rolesMap = [
            1 => 'Super Admin',
            2 => 'COO',
            3 => 'Project Manager',
            4 => 'HOD',
            5 => 'Others',
            6 => 'Engineer',
            7 => 'Reception',
            8 => 'Social Aid Manager',
            'super_admin'     => 'Super Admin',
            'coo'             => 'COO',
            'project_manager' => 'Project Manager',
            'hod'             => 'HOD',
            'engineer'        => 'Engineer',
            'reception'       => 'Reception',
            'social_aid'      => 'Social Aid Manager',
            'others'          => 'Others',
        ];

        $runningCount = 0;
        $completedCount = 0;
        if ($isPmOrEngineer && !empty($projects)) {
            foreach ($projects as $p) {
                $st = strtolower($p['status'] ?? '');
                if (in_array($st, ['completed', 'done', 'handover', 'finished'])) {
                    $completedCount++;
                } else {
                    $runningCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'name'            => $user->name,
                'email'           => $user->email,
                'mobile'          => $user->mobile ?? 'N/A',
                'father_name'     => $user->father_name ?? 'N/A',
                'mother_name'     => $user->mother_name ?? 'N/A',
                'date_of_birth'   => $user->date_of_birth ?? 'N/A',
                'date_of_joining' => $user->date_of_joining ?? 'N/A',
                'gender'          => $user->gender ?? 'N/A',
                'marital_status'  => $user->marital_status ?? 'N/A',
                'house_name'      => $user->house_name ?? 'N/A',
                'place'           => $user->place ?? 'N/A',
                'po'              => $user->po ?? 'N/A',
                'district'        => $user->district ?? 'N/A',
                'state'           => $user->state ?? 'N/A',
                'pin_code'        => $user->pin_code ?? 'N/A',
                'aadhar_number'   => $user->aadhar_number ?? 'N/A',
                'pan_card_number' => $user->pan_card_number ?? 'N/A',
                'account_number'  => $user->account_number ?? 'N/A',
                'bank_name'       => $user->bank_name ?? 'N/A',
                'bank_branch'     => $user->bank_branch ?? 'N/A',
                'ifsc_code'       => $user->ifsc_code ?? 'N/A',
                'designation'     => $user->designation ?? 'N/A',
                'role'            => $rolesMap[$user->role] ?? 'User',
                'raw_role'        => $user->role,
                'is_hr'           => (bool)$user->is_hr,
                'Is_hr'           => (bool)$user->is_hr,
                'assigned_hod_id' => $user->hod_id ?? $user->assigned_hod_id,
                'hod_id'          => $user->hod_id ?? $user->assigned_hod_id,
                'assigned_hod_name' => $user->assignedHod ? $user->assignedHod->name : null,
                'is_pm_or_engineer' => $isPmOrEngineer,
                'address'         => $user->profile->address ?? 'N/A',
                'is_suspended'    => $user->is_suspended,
                'photo_url'       => ($user->profile && $user->profile->photo) ? asset($user->profile->photo) : ($user->photo ? asset($user->photo) : null),
            ],
            'projects' => $projects,
            'running_projects_count' => $runningCount,
            'completed_projects_count' => $completedCount,
            'total_projects_count' => is_countable($projects) ? count($projects) : 0,
        ]);
    }

    public function toggleSuspend(Request $request, $id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot suspend your own logged-in account.'], 400);
            }
            return redirect()->route('users')->withErrors(['You cannot suspend your own logged-in account.']);
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $statusStr = $user->is_suspended ? 'suspended' : 'unsuspended';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_suspended' => $user->is_suspended,
                'status_label' => $user->is_suspended ? 'Suspended' : 'Active',
                'message' => "User account {$statusStr} successfully!"
            ]);
        }

        return redirect()->route('users')->with('success', "User account {$statusStr} successfully!");
    }

    public function submitLeaveRequest(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'reason'     => 'required|string|max:1000',
        ]);

        return redirect()->back()->with('success', "Your leave request ({$request->leave_type} from {$request->from_date} to {$request->to_date}) has been submitted successfully for approval!");
    }
}
