<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    protected $fillable = [
        'code',
        'name',
        'institution_name',
        'place',
        'po',
        'village',
        'panjayath',
        'dist',
        'state',
        'contact_no',
        'cordinator_name',
        'cordinator_contact_number',
        'remarks'
    ];

    public function orphanCareApplications()
    {
        return $this->hasMany(OrphanCareApplication::class, 'cluster_id');
    }
}
