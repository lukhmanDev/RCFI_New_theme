<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableRowChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $entityType,
        public string $operation,
        public array $row,
        public array $channels
    ) {}

    public function broadcastOn(): array
    {
        return array_map(fn ($c) => new PrivateChannel($c), array_unique($this->channels));
    }

    public function broadcastAs(): string
    {
        return "{$this->entityType}.{$this->operation}";
    }

    public function broadcastWith(): array
    {
        return array_merge($this->row, [
            'entity_type' => $this->entityType,
            'operation'   => $this->operation,
        ]);
    }
}
