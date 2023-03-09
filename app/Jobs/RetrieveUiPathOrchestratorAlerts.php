<?php

namespace App\Jobs;

use App\Models\OrchestratorConnection;
use App\Services\UiPathOrchestratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RetrieveUiPathOrchestratorAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UiPathOrchestratorService $service)
    {
        Log::info('Retrieving UiPath Orchestrator Alerts');
        $orchestratorConnections = OrchestratorConnection::all();
        foreach ($orchestratorConnections as $orchestratorConnection) {
            $tenants = $orchestratorConnection->tenants;
            foreach ($tenants as $tenant) {
                $result = $service->getAlerts($orchestratorConnection, $tenant);
                if ($result['ok']) {
                    $alerts = $result['alerts'];
                    foreach ($alerts as $alert) {
                        Log::debug($alert);
                        /*$alert = OrchestratorConnectionTenantAlert::create([
                            'external_id' => $alert['Id'],
                            'notification_name' => $alert['NotificationName'],
                            'data' => $alert['Data'],
                            'component' => $alert['Component'],
                            'severity' => $alert['Severity'],
                            'creation_time' => Carbon::createFromFormat('Y-m-dTH:i:s.u', $alert['CreationTime']),
                            'unread' => $alert['State'] == 'Unread',
                            'deep_link_relative_url' => $alert['DeepLinkRelativeUrl'],
                        ]);*/
                    }
                    Log::debug(count($alerts) . " alert(s) for tenant $tenant->name ($tenant->id) on UiPath Orchestrator $orchestratorConnection->name ($orchestratorConnection->id)");
                }
            }
        }
        Log::info('UiPath Orchestrator Alerts retrieved');
    }
}
