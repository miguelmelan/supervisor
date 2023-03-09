<?php

namespace App\Events;

use App\Models\OrchestratorConnection;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrchestratorConnectionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrchestratorConnection $orchestratorConnection;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OrchestratorConnection $orchestratorConnection)
    {
        $this->orchestratorConnection = $orchestratorConnection;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['orchestrator-connection'];
    }

    public function broadcastAs()
    {
        return 'new';
    }
}
