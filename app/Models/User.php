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
    ];

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
        ];

        return $map[$this->role] ?? ucwords(str_replace('_', ' ', $this->role));
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
}
