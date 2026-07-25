<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $projectId;
    public $category;
    public $userId;
    public $action;
    public $payload;

    /**
     * Create a new event instance.
     */
    public function __construct($projectId, $category = null, $userId = null, $action = 'updated', $payload = [])
    {
        $this->projectId = $projectId;
        $this->category = $category;
        $this->userId = $userId;
        $this->action = $action;
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('projects'),
            new Channel('project.' . $this->projectId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'project.updated';
    }
}
