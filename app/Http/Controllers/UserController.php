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
        $users = User::where('id', '!=', auth()->id())->orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['nullable', 'string', 'max:15'],
            'designation' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];

        if (auth()->user()->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', 'in:super_admin,coo,project_manager,hod,others,engineer,reception,Super Admin,COO,Project Manager,HOD,Others,Engineer,Reception,1,2,3,4,5,6,7'];
        }

        $data = $request->validate($rules);

        if (!auth()->user()->isSuperAdmin()) {
            $data['role'] = 'others'; // default to 'others'
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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile' => ['nullable', 'string', 'max:15'],
            'designation' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ];

        if (auth()->user()->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', 'in:super_admin,coo,project_manager,hod,others,engineer,reception,Super Admin,COO,Project Manager,HOD,Others,Engineer,Reception,1,2,3,4,5,6,7'];
        }

        $data = $request->validate($rules);

        if (!auth()->user()->isSuperAdmin()) {
            $data['role'] = $user->role;
            $data['designation'] = $user->designation;
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

        $rolesMap = [
            1 => 'Super Admin',
            2 => 'COO',
            3 => 'Project Manager',
            4 => 'HOD',
            5 => 'Others',
            6 => 'Engineer'
        ];

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile ?? 'N/A',
                'designation' => $user->designation ?? 'N/A',
                'role' => $rolesMap[$user->role] ?? 'User',
                'address' => $user->profile->address ?? 'N/A',
                'is_suspended' => $user->is_suspended,
            ],
            'projects' => $projects
        ]);
    }

    public function toggleSuspend($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return redirect()->route('users')->withErrors(['You cannot suspend your own logged-in account.']);
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $statusStr = $user->is_suspended ? 'suspended' : 'unsuspended';
        return redirect()->route('users')->with('success', "User account {$statusStr} successfully!");
    }
}
