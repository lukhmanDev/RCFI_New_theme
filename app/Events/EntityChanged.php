<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntityChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param string $entityType e.g. 'application', 'project', 'leaverequest'
     * @param string $operation 'created' | 'updated' | 'deleted' | 'approved' | 'rejected' | custom
     * @param array $payload Minimal render-ready data array
     * @param array $channels Array of private channel names
     */
    public function __construct(
        public string $entityType,
        public string $operation,
        public array $payload,
        public array $channels
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return array_map(fn ($c) => new PrivateChannel($c), array_unique($this->channels));
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return "{$this->entityType}.{$this->operation}";
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return array_merge($this->payload, [
            'entity_type' => $this->entityType,
            'operation'   => $this->operation,
        ]);
    }
}
