<?php

namespace App\Events;

use App\Http\Resources\OrchestratorConnectionTenantAlertResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrchestratorConnectionTenantAlertCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrchestratorConnectionTenantAlertResource $resource;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OrchestratorConnectionTenantAlertResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['orchestrator-connection-tenant-alert'];
    }

    public function broadcastAs()
    {
        return 'new';
    }
}
