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
        'role' => 'integer',
        'is_suspended' => 'boolean',
    ];

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
            \App\Models\OrphanCareProject::class,
            \App\Models\DifferentlyAbledProject::class,
            \App\Models\FamilyAidProject::class,
            \App\Models\GeneralProject::class,
        ];

        $projects = collect();

        foreach ($projectModels as $modelClass) {
            $categoryProjects = $modelClass::where('project_manager_id', $this->id)
                ->orWhere('engineer_id', $this->id)
                ->get();
            
            $projects = $projects->concat($categoryProjects);
        }

        return $projects->sortByDesc('created_at');
    }
}
