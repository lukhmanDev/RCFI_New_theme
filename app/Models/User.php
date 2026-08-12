<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
        'is_suspended' => 'boolean',
        'is_hr' => 'boolean',
        'Is_hr' => 'boolean',
        'hod_id' => 'integer',
        'assigned_hod_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if (isset($user->attributes['hod_id']) && !isset($user->attributes['assigned_hod_id'])) {
                $user->attributes['assigned_hod_id'] = $user->attributes['hod_id'];
            } elseif (isset($user->attributes['assigned_hod_id']) && !isset($user->attributes['hod_id'])) {
                $user->attributes['hod_id'] = $user->attributes['assigned_hod_id'];
            } elseif (isset($user->attributes['hod_id']) && isset($user->attributes['assigned_hod_id'])) {
                $val = $user->attributes['hod_id'] ?: $user->attributes['assigned_hod_id'];
                $user->attributes['hod_id'] = $val;
                $user->attributes['assigned_hod_id'] = $val;
            }

            if ($user->is_hr) {
                // Auto-flip rule: Ensure only one user can be is_hr = true system-wide
                static::where('id', '!=', $user->id)->where('is_hr', true)->update(['is_hr' => false]);
            }
        });
    }

    public function getRoleAttribute($value)
    {
        $map = [
            1 => 'super_admin',
            '1' => 'super_admin',
            'Super Admin' => 'super_admin',
            2 => 'coo',
            '2' => 'coo',
            'COO' => 'coo',
            3 => 'project_manager',
            '3' => 'project_manager',
            'Project Manager' => 'project_manager',
            4 => 'hod',
            '4' => 'hod',
            'HOD' => 'hod',
            5 => 'others',
            '5' => 'others',
            'Others' => 'others',
            6 => 'engineer',
            '6' => 'engineer',
            'Engineer' => 'engineer',
            7 => 'reception',
            '7' => 'reception',
            'Reception' => 'reception',
            'reception' => 'reception',
            8 => 'social_aid',
            '8' => 'social_aid',
            'Social Aid' => 'social_aid',
            'Social Aid Manager' => 'social_aid',
            'social_aid' => 'social_aid',
        ];

        return $map[$value] ?? strtolower(str_replace(' ', '_', $value ?: 'others'));
    }

    public function setRoleAttribute($value)
    {
        $map = [
            1 => 'super_admin',
            '1' => 'super_admin',
            'Super Admin' => 'super_admin',
            2 => 'coo',
            '2' => 'coo',
            'COO' => 'coo',
            3 => 'project_manager',
            '3' => 'project_manager',
            'Project Manager' => 'project_manager',
            4 => 'hod',
            '4' => 'hod',
            'HOD' => 'hod',
            5 => 'others',
            '5' => 'others',
            'Others' => 'others',
            6 => 'engineer',
            '6' => 'engineer',
            'Engineer' => 'engineer',
            7 => 'reception',
            '7' => 'reception',
            'Reception' => 'reception',
            'reception' => 'reception',
            8 => 'social_aid',
            '8' => 'social_aid',
            'Social Aid' => 'social_aid',
            'Social Aid Manager' => 'social_aid',
            'social_aid' => 'social_aid',
            9 => 'employee',
            '9' => 'employee',
            'Employee' => 'employee',
            'employee' => 'employee',
            'staff' => 'employee',
        ];

        $this->attributes['role'] = $map[$value] ?? strtolower(str_replace(' ', '_', $value ?: 'others'));
    }

    public function getRoleNameAttribute(): string
    {
        $map = [
            'super_admin' => 'Super Admin',
            'coo' => 'COO',
            'project_manager' => 'Project Manager',
            'hod' => 'HOD',
            'others' => 'Others',
            'engineer' => 'Engineer',
            'reception' => 'Reception',
            'social_aid' => 'Social Aid Manager',
            'employee' => 'Employee',
        ];

        return $map[$this->role] ?? ucwords(str_replace('_', ' ', $this->role));
    }

    public function scopeForHod($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return $query;
        }

        $isPureHod = ($user->isHod() && !$user->isSuperAdmin() && !$user->isCoo() && !(bool)$user->is_hr);

        if ($isPureHod) {
            return $query->where(function($q) use ($user) {
                $q->where('assigned_hod_id', $user->id)
                  ->orWhere('hod_id', $user->id);
            });
        }

        return $query;
    }

    public function isSuperAdmin(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        $desigLower = strtolower(trim($this->designation ?? ''));
        return in_array($roleLower, ['super_admin', 'super admin', '1']) || str_contains($desigLower, 'super admin') || str_contains($desigLower, 'super_admin');
    }

    public function isCoo(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        $desigLower = strtolower(trim($this->designation ?? ''));
        return in_array($roleLower, ['coo', 'chief operating officer', '2']) || str_contains($desigLower, 'coo') || str_contains($desigLower, 'chief operating officer');
    }

    public function isHod(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        $desigLower = strtolower(trim($this->designation ?? ''));
        return in_array($roleLower, ['hod', 'head of department', '4']) || str_contains($desigLower, 'hod') || str_contains($desigLower, 'head of department');
    }

    public function isPm(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['project_manager', 'project manager', '3']);
    }

    public function isEngineer(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['engineer', '6']);
    }

    public function isReception(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['reception', '7']);
    }

    public function isSocialAid(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['social_aid', 'social aid', 'social aid manager', '8']);
    }

    public function isOthers(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['others', 'other', '5']);
    }

    public function isOther(): bool
    {
        return $this->isOthers();
    }

    public function isEmployee(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['employee', 'staff', '9']);
    }

    public function canAddApplications(): bool
    {
        return !$this->isOthers() && !$this->isEmployee();
    }

    public function canAddEditProjects(): bool
    {
        $designationLower = strtolower(trim($this->designation ?? ''));
        $isCoo = ($this->isCoo() || $this->role == 2 || $this->role === 'coo' || str_contains($designationLower, 'coo'));
        $isHod = ($this->isHod() || $this->role == 4 || $this->role === 'hod' || str_contains($designationLower, 'hod'));
        $isSuperAdmin = ($this->isSuperAdmin() || $this->role == 1 || $this->role === 'super_admin');

        return $isSuperAdmin || $isCoo || $isHod;
    }

    public function canAssignApplications(): bool
    {
        $designationLower = strtolower(trim($this->designation ?? ''));
        $isPm = ($this->isPm() || $this->role == 3 || $this->role === 'project_manager' || str_contains($designationLower, 'project manager'));
        $isEngineer = ($this->isEngineer() || $this->role == 6 || $this->role === 'engineer' || str_contains($designationLower, 'engineer'));
        $isCoo = ($this->isCoo() || $this->role == 2 || $this->role === 'coo' || str_contains($designationLower, 'coo'));
        $isHod = ($this->isHod() || $this->role == 4 || $this->role === 'hod' || str_contains($designationLower, 'hod'));
        $isSuperAdmin = ($this->isSuperAdmin() || $this->role == 1 || $this->role === 'super_admin');

        return $isSuperAdmin || $isCoo || $isHod || $isPm || $isEngineer;
    }

    public function canDownloadExcel(): bool
    {
        return !$this->isOthers();
    }

    public function hasAdminAccess(): bool
    {
        $roleLower = strtolower(trim($this->role ?? ''));
        return in_array($roleLower, ['super_admin', 'super admin', 'coo', 'hod', 'reception', 'social_aid', '1', '2', '4', '7', '8']) || $this->isCoo() || $this->isHod() || $this->isSuperAdmin();
    }

    public function canApproveApplications(): bool
    {
        if ($this->isSocialAid()) {
            return false;
        }
        $designationLower = strtolower(trim($this->designation ?? ''));
        if (str_contains($designationLower, 'social aid') || str_contains($designationLower, 'social_aid')) {
            return false;
        }

        return $this->isCoo() || $this->isSuperAdmin() || $this->isHod();
    }

    public function canDeleteApplications(): bool
    {
        return false;
    }

    public function canManageSponsorship(): bool
    {
        if ($this->isSocialAid()) {
            return false;
        }
        $designationLower = strtolower($this->designation ?? '');
        if (str_contains($designationLower, 'social aid') || str_contains($designationLower, 'social_aid')) {
            return false;
        }

        return $this->isSuperAdmin() || $this->isCoo() || $this->isHod();
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile && $this->profile->photo) {
            return asset($this->profile->photo);
        }
        return 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT6WbkrAqlGF2Xzmb-prbginrkDNrv6zT05ID6KEjTbP2F-gn9w-wg1L3_NiSeXLq3HsqI&usqp=CAU';
    }

    public function getAssignedProjectsAttribute()
    {
        $projectModels = [
            \App\Models\EducationCenterProject::class,
            \App\Models\CulturalCenterProject::class,
            \App\Models\HospitalClinicProject::class,
            \App\Models\ShopOtherProject::class,
            \App\Models\HouseProject::class,
            \App\Models\DrinkingWaterGroupProject::class,
            \App\Models\DrinkingWaterIndividualProject::class,
            \App\Models\DifferentlyAbledProject::class,
            \App\Models\FamilyAidProject::class,
            \App\Models\GeneralProject::class,
            \App\Models\OrphanCareProject::class,
        ];

        $projects = collect();

        foreach ($projectModels as $modelClass) {
            $instance = new $modelClass;
            $table = $instance->getTable();

            $hasPm = \Illuminate\Support\Facades\Schema::hasColumn($table, 'project_manager_id');
            $hasEng = \Illuminate\Support\Facades\Schema::hasColumn($table, 'engineer_id');

            if (!$hasPm && !$hasEng) {
                continue;
            }

            $query = $modelClass::query();
            if ($hasPm && $hasEng) {
                $categoryProjects = $query->where(function ($q) {
                    $q->where('project_manager_id', $this->id)
                      ->orWhere('engineer_id', $this->id);
                })->get();
            } elseif ($hasPm) {
                $categoryProjects = $query->where('project_manager_id', $this->id)->get();
            } else {
                $categoryProjects = $query->where('engineer_id', $this->id)->get();
            }

            $projects = $projects->concat($categoryProjects);
        }

        return $projects->sortByDesc('created_at');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'user_id');
    }

    public function getHodIdAttribute($value)
    {
        return $value ?? $this->attributes['assigned_hod_id'] ?? null;
    }

    public function setHodIdAttribute($value)
    {
        $this->attributes['hod_id'] = $value;
        $this->attributes['assigned_hod_id'] = $value;
    }

    public function getAssignedHodIdAttribute($value)
    {
        return $value ?? $this->attributes['hod_id'] ?? null;
    }

    public function setAssignedHodIdAttribute($value)
    {
        $this->attributes['assigned_hod_id'] = $value;
        $this->attributes['hod_id'] = $value;
    }

    public function getIsHrUpperAttribute()
    {
        return (bool)($this->attributes['is_hr'] ?? false);
    }

    public function setIsHrUpperAttribute($value)
    {
        $this->attributes['is_hr'] = (bool)$value;
    }

    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function assignedHod()
    {
        return $this->belongsTo(User::class, 'assigned_hod_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'hod_id');
    }

    public function assignedStaff()
    {
        return $this->hasMany(User::class, 'assigned_hod_id');
    }

    public function isHrHod(): bool
    {
        return $this->isHod() && (bool)$this->is_hr;
    }

    public static function getHrHod(): ?User
    {
        return static::where('role', 'hod')->where('is_hr', true)->first()
            ?? static::where('is_hr', true)->first();
    }

    /**
     * DSA Tree representation of the organization hierarchy.
     * Root: COO
     *   ├── HOD 1
     *   │    ├── Staff A
     *   │    └── Staff B
     *   ├── HOD 2
     *   ├── HOD 3 (HR)
     */
    public static function getHierarchyTree(): array
    {
        $currentUser = auth()->user();

        if ($currentUser && $currentUser->isHod() && !$currentUser->isSuperAdmin() && !$currentUser->isCoo() && !$currentUser->is_hr) {
            $hod = static::with('assignedStaff')->find($currentUser->id) ?? $currentUser;
            return [
                'id' => $hod->id,
                'name' => $hod->name,
                'role' => $hod->role_name,
                'designation' => $hod->designation ?? 'Head of Department',
                'email' => $hod->email,
                'mobile' => $hod->mobile,
                'is_suspended' => (bool)$hod->is_suspended,
                'is_hr' => (bool)$hod->is_hr,
                'children' => ($hod->assignedStaff ?? collect())->map(function($staff) {
                    return [
                        'id' => $staff->id,
                        'name' => $staff->name,
                        'role' => $staff->role_name,
                        'designation' => $staff->designation ?? 'Staff Member',
                        'email' => $staff->email,
                        'mobile' => $staff->mobile,
                        'is_suspended' => (bool)$staff->is_suspended,
                        'is_hr' => (bool)$staff->is_hr,
                        'children' => [],
                    ];
                })->toArray(),
            ];
        }

        $coo = static::where('role', 'coo')->first()
            ?? static::where('role', 'super_admin')->first();

        $hods = static::where('role', 'hod')->with('assignedStaff')->get();

        $tree = [
            'id' => $coo ? $coo->id : null,
            'name' => $coo ? $coo->name : 'COO',
            'role' => $coo ? $coo->role_name : 'Chief Operating Officer',
            'designation' => $coo ? ($coo->designation ?? 'Chief Operating Officer') : 'Chief Operating Officer',
            'email' => $coo ? $coo->email : '',
            'mobile' => $coo ? $coo->mobile : '',
            'is_suspended' => $coo ? (bool)$coo->is_suspended : false,
            'is_hr' => false,
            'children' => [],
        ];

        foreach ($hods as $hod) {
            $hodNode = [
                'id' => $hod->id,
                'name' => $hod->name,
                'role' => $hod->role_name,
                'designation' => $hod->designation ?? 'Head of Department',
                'email' => $hod->email,
                'mobile' => $hod->mobile,
                'is_suspended' => (bool)$hod->is_suspended,
                'is_hr' => (bool)$hod->is_hr,
                'children' => [],
            ];

            foreach ($hod->assignedStaff as $staff) {
                $hodNode['children'][] = [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'role' => $staff->role_name,
                    'designation' => $staff->designation ?? 'Staff Member',
                    'email' => $staff->email,
                    'mobile' => $staff->mobile,
                    'is_suspended' => (bool)$staff->is_suspended,
                    'is_hr' => (bool)$staff->is_hr,
                    'children' => [],
                ];
            }

            $tree['children'][] = $hodNode;
        }

        return $tree;
    }

    public function isEligibleFor(LeaveType $type): bool
    {
        if (!$type->is_active) {
            return false;
        }

        // Other Leave (OL) can only be granted / assigned by HR HOD, COO, or Super Admin
        if ($type->leave_code === 'OL' || $type->leave_code === 'OTHER') {
            $actor = auth()->user() ?? $this;
            return $actor->isSuperAdmin() || $actor->isCoo() || (bool)$actor->is_hr;
        }

        // Leave Without Pay (LWP) is available to all users unconditionally
        if ($type->leave_code === 'LWP' || ($type->accrual_type === 'None' && $type->leave_code !== 'OL')) {
            return true;
        }

        // Gender check
        if ($type->applicable_gender !== 'All') {
            if (!$this->gender || strtolower($this->gender) !== strtolower($type->applicable_gender)) {
                return false;
            }
        }

        // Marital status check
        if ($type->requires_marital_status !== 'Any') {
            if (!$this->marital_status || strtolower($this->marital_status) !== strtolower($type->requires_marital_status)) {
                return false;
            }
        }

        // Service years check
        if ($type->min_service_years > 0) {
            if (!$this->date_of_joining) {
                return false;
            }
            $yearsOfService = \Carbon\Carbon::parse($this->date_of_joining)->diffInYears(now());
            if ($yearsOfService < $type->min_service_years) {
                return false;
            }
        }

        // Lifetime only check
        if ($type->is_lifetime_only) {
            $usedDays = LeaveBalance::where('user_id', $this->id)
                ->where('leave_type_id', $type->id)
                ->sum('used_days');
            if ($usedDays > 0) {
                return false;
            }
        }

        return true;
    }

    public function getCurrentLeaveAttribute(): array
    {
        $today = now()->format('Y-m-d');

        $activeLeave = $this->leaveRequests()
            ->where('status', 'Approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->with('leaveType')
            ->first();

        if ($activeLeave) {
            $code = strtolower($activeLeave->leaveType->leave_code ?? 'leave');
            return [
                'status' => 'On Leave',
                'badge_style' => 'background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;',
                'type' => $activeLeave->leaveType->leave_name ?? 'Leave',
                'code' => $code . '_leave',
                'dates' => \Carbon\Carbon::parse($activeLeave->start_date)->format('M d') . ' - ' . \Carbon\Carbon::parse($activeLeave->end_date)->format('M d'),
                'is_on_leave' => true,
            ];
        }

        $pendingLeave = $this->leaveRequests()
            ->where('status', 'Pending')
            ->where('start_date', '>=', $today)
            ->with('leaveType')
            ->first();

        if ($pendingLeave) {
            return [
                'status' => 'Pending Request',
                'badge_style' => 'background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;',
                'type' => $pendingLeave->leaveType->leave_name ?? 'Pending Leave',
                'code' => 'pending',
                'dates' => \Carbon\Carbon::parse($pendingLeave->start_date)->format('M d') . ' - ' . \Carbon\Carbon::parse($pendingLeave->end_date)->format('M d'),
                'is_on_leave' => false,
            ];
        }

        return [
            'status' => $this->is_suspended ? 'Suspended' : 'Active',
            'badge_style' => $this->is_suspended ? 'background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2;' : 'background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0;',
            'type' => 'No active leave',
            'code' => 'no_leave',
            'dates' => null,
            'is_on_leave' => false,
        ];
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class, 'user_id');
    }

    public function scopeNonSuperAdmin($query)
    {
        return $query->whereNotIn('role', ['super_admin', 'Super Admin', '1', 1])
                     ->where('email', '!=', 'sdigibeat@gmail.com');
    }

    public function getFormattedAadharNumberAttribute(): string
    {
        if (!$this->aadhar_number) {
            return '-';
        }
        $cleaned = preg_replace('/\D/', '', $this->aadhar_number);
        if (strlen($cleaned) === 12) {
            return substr($cleaned, 0, 4) . ' ' . substr($cleaned, 4, 4) . ' ' . substr($cleaned, 8, 4);
        }
        return $this->aadhar_number;
    }
}
