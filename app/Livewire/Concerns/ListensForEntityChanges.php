<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;

trait ListensForEntityChanges
{
    /**
     * Declares event names to watch, e.g. ['application.created', 'project.updated', 'leaverequest.approved']
     */
    abstract public function watchedEvents(): array;

    /**
     * Declares active channel names for the current user session
     */
    public function dashboardChannels(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $channels = [
            "role.{$user->role}",
            "user.{$user->id}",
        ];

        if ($user->isHod() && !empty($user->department_id)) {
            $channels[] = "role.hod.{$user->department_id}";
        }

        return array_unique($channels);
    }

    public function getListeners(): array
    {
        $listeners = [];
        $channels = $this->dashboardChannels();
        $watchedEvents = $this->watchedEvents();

        foreach ($channels as $channel) {
            foreach ($watchedEvents as $event) {
                $listeners["echo-private:{$channel},.{$event}"] = 'handleEntityChange';
            }
        }

        return $listeners;
    }

    public function handleEntityChange(array $payload = []): void
    {
        $entityType = $payload['entity_type'] ?? '';
        $operation  = $payload['operation'] ?? '';

        if (($entityType === 'application' && $operation === 'created') || (isset($payload['applicant_name']))) {
            if (method_exists($this, 'onNewApplication')) {
                $this->onNewApplication($payload);
                return;
            }
        }

        if (method_exists($this, 'patchLocalState')) {
            $this->patchLocalState($payload);
        } else {
            $this->dispatch('$refresh');
        }
    }
}
