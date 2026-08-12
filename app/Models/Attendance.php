<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'notes',
        'marked_by',
        'ip_address',
        'location',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function getFormattedClockInAttribute(): string
    {
        if (!$this->clock_in) return '--';
        return Carbon::parse($this->clock_in)->format('h:i A');
    }

    public function getFormattedClockOutAttribute(): string
    {
        if (!$this->clock_out) return '--';
        return Carbon::parse($this->clock_out)->format('h:i A');
    }

    public function getWorkingHoursAttribute(): string
    {
        if (!$this->clock_in || !$this->clock_out) return '--';

        $in = Carbon::parse($this->clock_in);
        $out = Carbon::parse($this->clock_out);

        if ($out->lt($in)) return '--';

        $minutes = $in->diffInMinutes($out);
        $hours = floor($minutes / 60);
        $remMinutes = $minutes % 60;

        if ($hours > 0 && $remMinutes > 0) {
            return "{$hours}h {$remMinutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$remMinutes}m";
        }
    }
}
