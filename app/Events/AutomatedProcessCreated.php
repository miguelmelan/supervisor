<?php

namespace App\Events;

use App\Models\AutomatedProcess;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AutomatedProcessCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AutomatedProcess $automatedProcess;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AutomatedProcess $automatedProcess)
    {
        $this->automatedProcess = $automatedProcess;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['automated-process'];
    }

    public function broadcastAs()
    {
        return 'new';
    }
}
