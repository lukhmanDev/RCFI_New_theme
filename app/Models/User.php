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
        ];

        return $map[$this->role] ?? ucwords(str_replace('_', ' ', $this->role));
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'Super Admin', '1', 1]);
    }

    public function isCoo(): bool
    {
        return in_array($this->role, ['coo', 'COO', '2', 2]);
    }

    public function isHod(): bool
    {
        return in_array($this->role, ['hod', 'HOD', '4', 4]);
    }

    public function isPm(): bool
    {
        return in_array($this->role, ['project_manager', 'Project Manager', '3', 3]);
    }

    public function isEngineer(): bool
    {
        return in_array($this->role, ['engineer', 'Engineer', '6', 6]);
    }

    public function isReception(): bool
    {
        return in_array($this->role, ['reception', 'Reception', '7', 7]);
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['super_admin', 'coo', 'hod', 'reception', 'Super Admin', 'COO', 'HOD', 'Reception', 1, 2, 4, 7, '1', '2', '4', '7']);
    }

    public function canApproveApplications(): bool
    {
        return $this->isCoo() || $this->isSuperAdmin();
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
