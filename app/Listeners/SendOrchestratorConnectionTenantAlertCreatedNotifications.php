<?php

namespace App\Listeners;

use App\Events\OrchestratorConnectionTenantAlertCreated;
use App\Notifications\NewOrchestratorConnectionTenantAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrchestratorConnectionTenantAlertCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrchestratorConnectionTenantAlertCreated  $event
     * @return void
     */
    public function handle(OrchestratorConnectionTenantAlertCreated $event)
    {
        Notification::route('slack', env('SLACK_HOOK'))->notify(new NewOrchestratorConnectionTenantAlert($event->resource));
    }
}
