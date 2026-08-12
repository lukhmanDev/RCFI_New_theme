<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BroadcastsChanges;
use App\Traits\BroadcastsTableChanges;

class LeaveRequest extends Model
{
    use BroadcastsChanges, BroadcastsTableChanges;

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'is_half_day',
        'half_day_session',
        'reason',
        'status',
        'applied_on',
        'approved_by',
        'approved_on',
        'remarks',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'applied_on'  => 'datetime',
        'approved_on' => 'datetime',
        'total_days'  => 'float',
        'is_half_day' => 'boolean',
    ];

    public function toBroadcastArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'leave_type_id' => $this->leave_type_id,
            'status' => $this->status ?? 'Pending',
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'total_days' => $this->total_days,
            'is_half_day' => $this->is_half_day,
            'half_day_session' => $this->half_day_session,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : now()->toIso8601String(),
        ];
    }

    public function broadcastChannels(): array
    {
        return [
            'role.super_admin',
            'role.coo',
            'role.hod',
            'role.project_manager',
            'role.social_aid',
            'user.' . $this->user_id,
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
