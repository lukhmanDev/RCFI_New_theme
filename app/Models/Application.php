<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BroadcastsChanges;
use App\Traits\BroadcastsTableChanges;

class Application extends Model
{
    use BroadcastsChanges, BroadcastsTableChanges;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];

    public function toBroadcastArray(): array
    {
        return [
            'id' => $this->id,
            'applicant_name' => $this->name ?? $this->applicant_name ?? 'N/A',
            'category' => $this->category ?? 'General',
            'status' => $this->status ?? 'Pending',
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : now()->toIso8601String(),
        ];
    }

    public function broadcastChannels(): array
    {
        return [
            'role.super_admin',
            'role.coo',
            'role.project_manager',
            'role.social_aid',
            'role.reception',
        ];
    }

    public function toTableRowArray(): array
    {
        return $this->toBroadcastArray();
    }

    public function tableChannels(): array
    {
        return $this->broadcastChannels();
    }
}
