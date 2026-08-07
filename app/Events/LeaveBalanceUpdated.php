<?php

namespace App\Events;

use App\Models\LeaveBalance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveBalanceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaveBalance;

    public function __construct(LeaveBalance $leaveBalance)
    {
        $this->leaveBalance = $leaveBalance->load(['user', 'leaveType']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->leaveBalance->user_id),
        ];
    }
}
