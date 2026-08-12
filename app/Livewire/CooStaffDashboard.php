<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\Project;
use App\Services\LeaveService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CooStaffDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'directory';
    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $viewMode = 'grid'; // 'grid' or 'table'

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['grid', 'table']) ? $mode : 'grid';
    }

    // Selected Staff Modal state
    public ?int $selectedUserId = null;
    public bool $showStaffModal = false;

    // Edit Staff Modal state
    public bool $showEditStaffModal = false;
    public ?int $editUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editMobile = '';
    public string $editDesignation = '';
    public string $editRole = '';
    public string $editFatherName = '';
    public string $editMotherName = '';
    public string $editDateOfBirth = '';
    public string $editDateOfJoining = '';
    public string $editGender = 'Male';
    public string $editMaritalStatus = 'Single';
    public string $editHouseName = '';
    public string $editPlace = '';
    public string $editPo = '';
    public string $editDistrict = '';
    public string $editState = '';
    public string $editPinCode = '';
    public string $editAadharNumber = '';
    public string $editPanCardNumber = '';
    public string $editAccountNumber = '';
    public string $editBankName = '';
    public string $editBankBranch = '';
    public string $editIfscCode = '';

    // Add Staff Modal state
    public bool $showAddStaffModal = false;
    public ?int $addHodId = null;
    public ?int $editHodId = null;
    public bool $addIsHr = false;
    public bool $editIsHr = false;
    public string $addName = '';
    public string $addEmail = '';
    public string $addMobile = '';
    public string $addDesignation = '';
    public string $addRole = 'engineer';
    public string $addFatherName = '';
    public string $addMotherName = '';
    public string $addDateOfBirth = '';
    public string $addDateOfJoining = '';
    public string $addGender = 'Male';
    public string $addMaritalStatus = 'Single';
    public string $addHouseName = '';
    public string $addPlace = '';
    public string $addPo = '';
    public string $addDistrict = '';
    public string $addState = 'Kerala';
    public string $addPinCode = '';
    public string $addAadharNumber = '';
    public string $addPanCardNumber = '';
    public string $addAccountNumber = '';
    public string $addBankName = '';
    public string $addBankBranch = '';
    public string $addIfscCode = '';
    public string $addPassword = '';

    // Leave Reject Modal state
    public bool $showRejectLeaveModal = false;
    public ?int $rejectLeaveId = null;
    public string $rejectRemarks = '';

    public string $successMessage = '';
    public string $errorMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAddRole($value): void
    {
        if ($value === 'hod') {
            $this->addHodId = null;
        } else {
            $this->addIsHr = false;
        }
    }

    public function updatedEditRole($value): void
    {
        if ($value === 'hod') {
            $this->editHodId = null;
        } else {
            $this->editIsHr = false;
        }
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function viewStaff(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->showStaffModal = true;
    }

    public function closeStaffModal(): void
    {
        $this->showStaffModal = false;
        $this->selectedUserId = null;
    }

    public function openEditStaffModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editUserId = $user->id;
        $this->editName = $user->name ?? '';
        $this->editEmail = $user->email ?? '';
        $this->editMobile = $user->mobile ?? '';
        $this->editDesignation = $user->designation ?? '';
        $this->editRole = $user->role ?? 'others';
        $this->editFatherName = $user->father_name ?? '';
        $this->editMotherName = $user->mother_name ?? '';
        $this->editDateOfBirth = $user->date_of_birth ? Carbon::parse($user->date_of_birth)->format('Y-m-d') : '';
        $this->editDateOfJoining = $user->date_of_joining ? Carbon::parse($user->date_of_joining)->format('Y-m-d') : '';
        $this->editGender = $user->gender ?? 'Male';
        $this->editMaritalStatus = $user->marital_status ?? 'Single';
        $this->editHouseName = $user->house_name ?? '';
        $this->editPlace = $user->place ?? '';
        $this->editPo = $user->po ?? '';
        $this->editDistrict = $user->district ?? '';
        $this->editState = $user->state ?? '';
        $this->editPinCode = $user->pin_code ?? '';
        $this->editAadharNumber = $user->aadhar_number ?? '';
        $this->editPanCardNumber = $user->pan_card_number ?? '';
        $this->editAccountNumber = $user->account_number ?? '';
        $this->editBankName = $user->bank_name ?? '';
        $this->editBankBranch = $user->bank_branch ?? '';
        $this->editIfscCode = $user->ifsc_code ?? '';
        $this->editHodId = $user->hod_id ?? $user->assigned_hod_id;
        $this->editIsHr = (bool)$user->is_hr;

        $this->showEditStaffModal = true;
    }

    public function closeEditStaffModal(): void
    {
        $this->showEditStaffModal = false;
        $this->editUserId = null;
    }

    public function saveStaffDetails(): void
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isSuperAdmin() && !$currentUser->isCoo() && !$currentUser->isHod())) {
            abort(403, 'Unauthorized to edit staff details.');
        }

        $this->validate([
            'editName' => 'required|string|min:2|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,' . $this->editUserId,
            'editMobile' => 'required|string|regex:/^[0-9]{10}$/',
            'editDesignation' => 'required|string|max:255',
            'editRole' => 'required|string',
            'editFatherName' => 'nullable|string|max:255',
            'editMotherName' => 'nullable|string|max:255',
            'editDateOfBirth' => 'nullable|date',
            'editDateOfJoining' => 'nullable|date',
            'editGender' => 'nullable|string',
            'editMaritalStatus' => 'nullable|string',
            'editHouseName' => 'nullable|string|max:255',
            'editPlace' => 'nullable|string|max:255',
            'editPo' => 'nullable|string|max:255',
            'editDistrict' => 'nullable|string|max:255',
            'editState' => 'nullable|string|max:255',
            'editPinCode' => 'nullable|string|regex:/^[0-9]{6}$/',
            'editAadharNumber' => 'nullable|string|max:20',
            'editPanCardNumber' => 'nullable|string|max:20',
            'editAccountNumber' => 'nullable|string|max:30',
            'editBankName' => 'nullable|string|max:255',
            'editBankBranch' => 'nullable|string|max:255',
            'editIfscCode' => 'nullable|string|max:20',
        ], [
            'editMobile.regex' => 'Phone number must be exactly 10 digits.',
            'editPinCode.regex' => 'PIN code must be exactly 6 digits.',
        ]);

        $user = User::findOrFail($this->editUserId);

        $user->update([
            'name' => trim($this->editName),
            'email' => trim($this->editEmail),
            'mobile' => trim($this->editMobile),
            'designation' => trim($this->editDesignation),
            'role' => $this->editRole,
            'father_name' => $this->editFatherName ?: null,
            'mother_name' => $this->editMotherName ?: null,
            'date_of_birth' => $this->editDateOfBirth ?: null,
            'date_of_joining' => $this->editDateOfJoining ?: null,
            'gender' => $this->editGender ?: null,
            'marital_status' => $this->editMaritalStatus ?: null,
            'house_name' => $this->editHouseName ?: null,
            'place' => $this->editPlace ?: null,
            'po' => $this->editPo ?: null,
            'district' => $this->editDistrict ?: null,
            'state' => $this->editState ?: null,
            'pin_code' => $this->editPinCode ?: null,
            'aadhar_number' => $this->editAadharNumber ?: null,
            'pan_card_number' => $this->editPanCardNumber ? strtoupper(trim($this->editPanCardNumber)) : null,
            'account_number' => $this->editAccountNumber ?: null,
            'bank_name' => $this->editBankName ?: null,
            'bank_branch' => $this->editBankBranch ?: null,
            'ifsc_code' => $this->editIfscCode ? strtoupper(trim($this->editIfscCode)) : null,
            'hod_id' => ($this->editRole === 'hod') ? null : ($this->editHodId ?: null),
            'assigned_hod_id' => ($this->editRole === 'hod') ? null : ($this->editHodId ?: null),
            'is_hr' => ($this->editRole === 'hod') ? $this->editIsHr : false,
        ]);

        $this->showEditStaffModal = false;
        $this->successMessage = "Staff details for '{$user->name}' updated successfully!";
    }

    public function toggleUserSuspend(int $userId): void
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isSuperAdmin() && !$currentUser->isCoo())) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($userId);
        if ($user->id === $currentUser->id) {
            $this->errorMessage = 'You cannot suspend your own account.';
            return;
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $statusText = $user->is_suspended ? 'suspended' : 'activated';
        $this->successMessage = "User {$user->name} has been {$statusText}.";
    }

    public function openAddStaffModal(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            $this->errorMessage = 'Only Super Admin can register new staff members.';
            return;
        }
        $this->resetAddStaffForm();
        $this->showAddStaffModal = true;
    }

    public function closeAddStaffModal(): void
    {
        $this->showAddStaffModal = false;
    }

    public function resetAddStaffForm(): void
    {
        $this->addHodId = null;
        $this->addIsHr = false;
        $this->addName = '';
        $this->addEmail = '';
        $this->addMobile = '';
        $this->addDesignation = '';
        $this->addRole = 'engineer';
        $this->addFatherName = '';
        $this->addMotherName = '';
        $this->addDateOfBirth = '';
        $this->addDateOfJoining = now()->format('Y-m-d');
        $this->addGender = 'Male';
        $this->addMaritalStatus = 'Single';
        $this->addHouseName = '';
        $this->addPlace = '';
        $this->addPo = '';
        $this->addDistrict = '';
        $this->addState = 'Kerala';
        $this->addPinCode = '';
        $this->addAadharNumber = '';
        $this->addPanCardNumber = '';
        $this->addAccountNumber = '';
        $this->addBankName = '';
        $this->addBankBranch = '';
        $this->addIfscCode = '';
        $this->addPassword = '';
    }

    public function updatedAddAadharNumber($val): void
    {
        $cleaned = preg_replace('/\D/', '', $val);
        if (strlen($cleaned) > 12) {
            $cleaned = substr($cleaned, 0, 12);
        }
        $parts = str_split($cleaned, 4);
        $this->addAadharNumber = implode(' ', $parts);
    }

    public function updatedEditAadharNumber($val): void
    {
        $cleaned = preg_replace('/\D/', '', $val);
        if (strlen($cleaned) > 12) {
            $cleaned = substr($cleaned, 0, 12);
        }
        $parts = str_split($cleaned, 4);
        $this->editAadharNumber = implode(' ', $parts);
    }

    public function createStaff(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            $this->errorMessage = 'Only Super Admin can register new staff members.';
            return;
        }

        $this->validate([
            'addName'           => 'required|string|min:2|max:255',
            'addEmail'          => 'required|string|email|max:255|unique:users,email',
            'addMobile'         => 'required|string|regex:/^[0-9]{10}$/',
            'addFatherName'    => 'required|string|max:255',
            'addMotherName'    => 'required|string|max:255',
            'addDateOfBirth'  => 'required|date',
            'addDateOfJoining' => 'required|date',
            'addGender'         => 'required|string|in:Male,Female,Other',
            'addMaritalStatus' => 'required|string|in:Single,Married,Divorced,Widowed',
            'addHouseName'     => 'required|string|max:255',
            'addPlace'          => 'required|string|max:255',
            'addPo'             => 'required|string|max:255',
            'addDistrict'       => 'required|string|max:255',
            'addState'          => 'required|string|max:255',
            'addPinCode'       => 'required|string|regex:/^[0-9]{6}$/',
            'addAadharNumber'  => 'required|string|max:20',
            'addPanCardNumber' => 'required|string|max:20',
            'addAccountNumber' => 'required|string|max:30',
            'addBankName'      => 'required|string|max:255',
            'addBankBranch'    => 'required|string|max:255',
            'addIfscCode'      => 'required|string|max:20',
            'addDesignation'    => 'required|string|max:255',
            'addRole'           => 'required|string',
            'addPassword'       => 'required|string|min:8',
            'addHodId'          => in_array($this->addRole, ['super_admin', 'coo', 'hod']) ? 'nullable|exists:users,id' : 'required|exists:users,id',
        ], [
            'addMobile.regex' => 'Phone number must be exactly 10 digits.',
            'addPinCode.regex' => 'PIN code must be exactly 6 digits.',
            'addHodId.required' => 'Please select an assigned HOD for this staff member.',
        ]);

        try {
            $user = User::create([
                'hod_id'          => ($this->addRole === 'hod') ? null : ($this->addHodId ?: null),
                'assigned_hod_id' => ($this->addRole === 'hod') ? null : ($this->addHodId ?: null),
                'is_hr'           => ($this->addRole === 'hod') ? $this->addIsHr : false,
                'name'            => $this->addName,
                'email'           => $this->addEmail,
                'mobile'          => $this->addMobile,
                'father_name'     => $this->addFatherName,
                'mother_name'     => $this->addMotherName,
                'date_of_birth'   => $this->addDateOfBirth,
                'date_of_joining' => $this->addDateOfJoining,
                'gender'          => $this->addGender,
                'marital_status'  => $this->addMaritalStatus,
                'house_name'      => $this->addHouseName,
                'place'           => $this->addPlace,
                'po'              => $this->addPo,
                'district'        => $this->addDistrict,
                'state'           => $this->addState,
                'pin_code'        => $this->addPinCode,
                'aadhar_number'   => $this->addAadharNumber,
                'pan_card_number' => strtoupper($this->addPanCardNumber),
                'account_number'  => $this->addAccountNumber,
                'bank_name'       => $this->addBankName,
                'bank_branch'     => $this->addBankBranch,
                'ifsc_code'       => strtoupper($this->addIfscCode),
                'designation'     => $this->addDesignation,
                'role'            => $this->addRole,
                'password'        => bcrypt($this->addPassword),
            ]);

            $this->successMessage = "Staff member {$user->name} registered successfully!";
            $this->closeAddStaffModal();
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to create staff: ' . $e->getMessage();
        }
    }

    public function approveLeave(int $leaveRequestId, LeaveService $leaveService): void
    {
        $currentUser = Auth::user();
        $req = LeaveRequest::findOrFail($leaveRequestId);

        try {
            $leaveService->approve($req, $currentUser);
            $this->successMessage = "Leave request #{$req->id} for {$req->user->name} has been approved.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function openRejectLeaveModal(int $leaveRequestId): void
    {
        $this->rejectLeaveId = $leaveRequestId;
        $this->rejectRemarks = '';
        $this->showRejectLeaveModal = true;
    }

    public function closeRejectLeaveModal(): void
    {
        $this->showRejectLeaveModal = false;
        $this->rejectLeaveId = null;
    }

    public function confirmRejectLeave(LeaveService $leaveService): void
    {
        $this->validate([
            'rejectRemarks' => 'required|string|max:1000',
        ]);

        $currentUser = Auth::user();
        $req = LeaveRequest::findOrFail($this->rejectLeaveId);

        try {
            $leaveService->reject($req, $currentUser, $this->rejectRemarks);
            $this->successMessage = "Leave request #{$req->id} rejected successfully.";
            $this->showRejectLeaveModal = false;
            $this->rejectLeaveId = null;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $today = now()->format('Y-m-d');
        $currentUser = Auth::user();

        // Staff Directory Query (Hides Super Admin, Logged in User & Scoped for HOD)
        $staffQuery = User::nonSuperAdmin()
            ->where('id', '!=', $currentUser->id)
            ->forHod($currentUser)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('mobile', 'like', '%' . $this->search . '%')
                        ->orWhere('designation', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->when($this->statusFilter === 'active', fn($q) => $q->where('is_suspended', false))
            ->when($this->statusFilter === 'suspended', fn($q) => $q->where('is_suspended', true))
            ->orderBy('name', 'asc');

        $staffList = $staffQuery->paginate(15);

        // Fetch Today's Attendance Keyed by User ID (non-super-admin, excluding logged-in user & forHod)
        $nonSuperAdminIds = User::nonSuperAdmin()->where('id', '!=', $currentUser->id)->forHod($currentUser)->pluck('id');
        $todayAttendances = Attendance::where('date', $today)
            ->whereIn('user_id', $nonSuperAdminIds)
            ->get()
            ->keyBy('user_id');

        // Overall Operational Statistics (Excludes Super Admin, Logged-in User & Scoped for HOD)
        $totalStaffCount = User::nonSuperAdmin()->where('id', '!=', $currentUser->id)->forHod($currentUser)->count();
        $activeStaffCount = User::nonSuperAdmin()->where('id', '!=', $currentUser->id)->forHod($currentUser)->where('is_suspended', false)->count();
        $suspendedStaffCount = User::nonSuperAdmin()->where('id', '!=', $currentUser->id)->forHod($currentUser)->where('is_suspended', true)->count();

        $presentTodayCount = $todayAttendances->whereIn('status', ['Present', 'Late'])->count();
        $lateTodayCount = $todayAttendances->where('status', 'Late')->count();
        $absentTodayCount = $todayAttendances->where('status', 'Absent')->count();
        $onLeaveTodayCount = $todayAttendances->where('status', 'OnLeave')->count();

        $attendanceRate = $activeStaffCount > 0 ? round(($presentTodayCount / $activeStaffCount) * 100, 1) : 0;

        // Pending Leave Requests Queue (Excludes Super Admin & Scoped for HOD)
        $pendingLeaveRequests = LeaveRequest::with(['user', 'leaveType'])
            ->whereHas('user', fn($q) => $q->nonSuperAdmin()->forHod($currentUser))
            ->where('status', 'Pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // Staff currently on active leave today (Excludes Super Admin & Scoped for HOD)
        $staffOnLeaveToday = LeaveRequest::with(['user', 'leaveType'])
            ->whereHas('user', fn($q) => $q->nonSuperAdmin()->forHod($currentUser))
            ->where('status', 'Approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        // Role Distribution Summary (Excludes Super Admin, Logged-in User & Scoped for HOD)
        $roleCounts = User::nonSuperAdmin()
            ->where('id', '!=', $currentUser->id)
            ->forHod($currentUser)
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Details for Selected Staff Member (Modal)
        $selectedUser = $this->selectedUserId ? User::find($this->selectedUserId) : null;
        $selectedUserBalances = $selectedUser ? LeaveBalance::with('leaveType')->where('user_id', $selectedUser->id)->get() : collect();
        
        $selectedUserProjects = collect();
        if ($selectedUser) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('projects')) {
                    $selectedUserProjects = Project::where('engineer_id', $selectedUser->id)
                        ->orWhere('project_manager_id', $selectedUser->id)
                        ->get();
                } else {
                    $selectedUserProjects = $selectedUser->assigned_projects;
                }
            } catch (\Throwable $e) {
                $selectedUserProjects = $selectedUser->assigned_projects;
            }
        }

        $selectedUserAttendance = $selectedUser ? Attendance::where('user_id', $selectedUser->id)->orderBy('date', 'desc')->take(10)->get() : collect();

        $hods = User::whereIn('role', ['hod', '4', 'HOD'])->orWhere('is_hr', true)->orderBy('name')->get();
        $hierarchyTree = User::getHierarchyTree();

        $lwpType = \App\Models\LeaveType::where('leave_code', 'LWP')->first();
        $lwpBalances = collect();
        if ($lwpType) {
            $lwpBalances = LeaveBalance::where('leave_type_id', $lwpType->id)
                ->whereIn('user_id', $staffList->pluck('id'))
                ->get()
                ->keyBy('user_id');
        }

        return view('livewire.coo-staff-dashboard', [
            'hods'                  => $hods,
            'hierarchyTree'         => $hierarchyTree,
            'lwpBalances'           => $lwpBalances,
            'staffList'             => $staffList,
            'todayAttendances'      => $todayAttendances,
            'totalStaffCount'       => $totalStaffCount,
            'activeStaffCount'      => $activeStaffCount,
            'suspendedStaffCount'   => $suspendedStaffCount,
            'presentTodayCount'     => $presentTodayCount,
            'lateTodayCount'        => $lateTodayCount,
            'absentTodayCount'      => $absentTodayCount,
            'onLeaveTodayCount'     => $onLeaveTodayCount,
            'attendanceRate'        => $attendanceRate,
            'pendingLeaveRequests'  => $pendingLeaveRequests,
            'staffOnLeaveToday'     => $staffOnLeaveToday,
            'roleCounts'            => $roleCounts,
            'selectedUser'          => $selectedUser,
            'selectedUserBalances'  => $selectedUserBalances,
            'selectedUserProjects'  => $selectedUserProjects,
            'selectedUserAttendance' => $selectedUserAttendance,
        ]);
    }
}
