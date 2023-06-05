<?php

namespace App\Events;

use App\Models\AIBasedAlertTrigger;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AIBasedAlertTriggerCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AIBasedAlertTrigger $alertTrigger;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AIBasedAlertTrigger $alertTrigger)
    {
        $this->alertTrigger = $alertTrigger;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['ai-based-alert-trigger'];
    }

    public function broadcastAs()
    {
        return 'new';
    }
}
