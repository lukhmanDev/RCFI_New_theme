<?php

namespace App\Traits;

use App\Events\EntityChanged;

trait BroadcastsChanges
{
    protected static function bootBroadcastsChanges(): void
    {
        static::created(function ($model) {
            $model->broadcastChange('created');
        });

        static::updated(function ($model) {
            // Guard against over-broadcasting trivial fields if model defines tracked fields
            if (method_exists($model, 'trackedBroadcastFields')) {
                $tracked = $model->trackedBroadcastFields();
                if (!empty($tracked) && !$model->wasChanged($tracked)) {
                    return;
                }
            }
            $model->broadcastChange('updated');
        });

        static::deleted(function ($model) {
            $model->broadcastChange('deleted');
        });
    }

    public function broadcastChange(string $operation, array $extraPayload = []): void
    {
        $channels = $this->broadcastChannels();
        if (empty($channels)) {
            return;
        }

        broadcast(new EntityChanged(
            entityType: $this->broadcastEntityType(),
            operation: $operation,
            payload: array_merge($this->toBroadcastArray(), $extraPayload),
            channels: $channels
        ))->toOthers();
    }

    public function broadcastEntityType(): string
    {
        if (property_exists($this, 'broadcastEntityType') && !empty($this->broadcastEntityType)) {
            return $this->broadcastEntityType;
        }
        return strtolower(class_basename($this));
    }

    /**
     * Minimal render-ready payload to broadcast.
     */
    abstract public function toBroadcastArray(): array;

    /**
     * Private channel names that care about updates to this entity.
     */
    abstract public function broadcastChannels(): array;
}
