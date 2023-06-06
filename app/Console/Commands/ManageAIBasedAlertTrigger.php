<?php

namespace App\Console\Commands;

use App\Events\OrchestratorConnectionTenantAlertCreated;
use App\Http\Resources\OrchestratorConnectionResource;
use App\Models\AIBasedAlertTrigger;
use App\Models\OrchestratorConnectionTenantAlert;
use App\Services\PythonService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ManageAIBasedAlertTrigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger:manage {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage AI Based Alert Trigger';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PythonService $service)
    {
        Log::info('Managing AI Based Alert Trigger');

        $trigger = AIBasedAlertTrigger::find($this->argument('id'));
        $tenants = $trigger->orchestratorConnectionTenants()->get();
        $triggerReleases = $trigger->releases();
        $triggerMachines = $trigger->machines();
        $triggerQueues = $trigger->queues();

        $verifications = array();
        foreach ($tenants as $tenant) {
            $tenantId = $tenant->id;
            $orchestratorConnectionId = $tenant->orchestratorConnection->id;

            $releasesByFolder = array();
            foreach ($triggerReleases->where('tenant_id', $tenantId)->get() as $release) {
                $folder = $release->external_folder_id;
                $id = $release->external_id;
                if (!array_key_exists($folder, $releasesByFolder)) {
                    $releasesByFolder[$folder] = array();
                }
                array_push($releasesByFolder[$folder], $id);
            }

            $machinesByFolder = array();
            foreach ($triggerMachines->where('tenant_id', $tenantId)->get() as $machine) {
                $folder = $machine->external_folder_id;
                $id = $machine->external_id;
                if (!array_key_exists($folder, $machinesByFolder)) {
                    $machinesByFolder[$folder] = array();
                }
                array_push($machinesByFolder[$folder], $id);
            }

            $queuesByFolder = array();
            foreach ($triggerQueues->where('tenant_id', $tenantId)->get() as $queue) {
                $folder = $queue->external_folder_id;
                $id = $queue->external_id;
                if (!array_key_exists($folder, $queuesByFolder)) {
                    $queuesByFolder[$folder] = array();
                }
                array_push($queuesByFolder[$folder], $id);
            }

            $output = $service->computeVerificationsForTenant($trigger->conditions, $orchestratorConnectionId, $tenantId, $releasesByFolder, $machinesByFolder, $queuesByFolder);
            Log::info(json_encode($output));

            foreach ($output->verifications as $verification) {
                $dataSource = $verification->data_source;
                $answer = $verification->answer;
                if (!property_exists($answer, 'error') && $answer->result) {
                    $notificationName = 'Supervisor.AI';
                    $component = 'Unknown';
                    if ($dataSource === 'Jobs execution history' || $dataSource === 'Robots logs') {
                        $component = 'Jobs';
                    } elseif ($dataSource === 'Machines details') {
                        $component = 'Machines';
                    } elseif ($dataSource === 'Queue items details') {
                        $component = 'Transactions';
                    }
                    $severity = 'Error';
                    $creationTime = Carbon::now();

                    $alerts = OrchestratorConnectionTenantAlert::search(json_encode($answer, true))
                        ->where('trigger_id', $trigger->id)
                        ->where('notification_name', $notificationName)
                        ->where('component', $component)
                        ->where('severity', $severity)
                        ->get()
                        ->whereNull('read_at');

                    if ($alerts->count() === 0) {
                        $alert = $tenant->alerts()->create([
                            'notification_name' => $notificationName,
                            'data' => json_encode($answer, true),
                            'component' => $component,
                            'severity' => $severity,
                            'creation_time' => $creationTime,
                            'trigger_id' => $trigger->id,
                        ]);
                        OrchestratorConnectionTenantAlertCreated::dispatch($alert);
                        Log::info("New alert created: $alert->id");
                    } else {
                        Log::info('No new alert created as an existing one is still pending');
                    }
                }
            }
            $tenantsOutput[$tenantId] = $output;
        }

        array_push($verifications, [
            'orchestrator_connection' => new OrchestratorConnectionResource($tenant->orchestratorConnection),
            'tenants' => $tenantsOutput,
        ]);
        
        $trigger->verifications = $verifications;
        $trigger->save();

        Log::info('AI Based Alert Trigger managed');
    }
}