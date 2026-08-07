<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public mixed $application) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('role.project_manager'),
            new PrivateChannel('role.coo'),
            new PrivateChannel('role.super_admin'),
            new PrivateChannel('role.reception'),
            new PrivateChannel('role.social_aid'),
        ];

        if (isset($this->application->department_id) && !empty($this->application->department_id)) {
            $channels[] = new PrivateChannel('role.hod.' . $this->application->department_id);
        } else {
            $channels[] = new PrivateChannel('role.hod');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'application.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->application->id ?? 0,
            'applicant_name' => $this->application->applicant_name ?? $this->application->name ?? 'N/A',
            'category' => $this->application->category ?? 'General',
            'status' => $this->application->status ?? 'Pending',
            'created_at' => isset($this->application->created_at) ? $this->application->created_at->toIso8601String() : now()->toIso8601String(),
        ];
    }
}
