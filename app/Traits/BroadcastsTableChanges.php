<?php

namespace App\Traits;

use App\Events\TableRowChanged;

trait BroadcastsTableChanges
{
    protected static function bootBroadcastsTableChanges(): void
    {
        static::created(fn ($model) => $model->broadcastTableChange('created'));
        static::updated(function ($model) {
            if (method_exists($model, 'trackedBroadcastFields')) {
                $tracked = $model->trackedBroadcastFields();
                if (!empty($tracked) && !$model->wasChanged($tracked)) {
                    return;
                }
            }
            $model->broadcastTableChange('updated');
        });
        static::deleted(fn ($model) => $model->broadcastTableChange('deleted'));
    }

    public function broadcastTableChange(string $operation, array $extraPayload = []): void
    {
        $channels = $this->tableChannels();
        if (empty($channels)) {
            return;
        }

        broadcast(new TableRowChanged(
            entityType: $this->tableEntityType(),
            operation: $operation,
            row: array_merge($this->toTableRowArray(), $extraPayload),
            channels: $channels
        ))->toOthers();
    }

    public function tableEntityType(): string
    {
        if (property_exists($this, 'tableEntityType') && !empty($this->tableEntityType)) {
            return $this->tableEntityType;
        }
        return strtolower(class_basename($this));
    }

    abstract public function toTableRowArray(): array;
    abstract public function tableChannels(): array;
}
