<?php

namespace App\Services;

use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use OzdemirBurak\JsonCsv\File\Json;
use Illuminate\Support\Facades\Storage;

class PythonService
{
    protected $env;
    protected UiPathOrchestratorService $orchestratorService;

    public function __construct(UiPathOrchestratorService $service)
    {
        $this->orchestratorService = $service;
        $this->env = [
            'OPENAI_API_TYPE' => env('OPENAI_API_TYPE'),
            'OPENAI_API_BASE' => env('OPENAI_API_BASE'),
            'OPENAI_API_VERSION' => env('OPENAI_API_VERSION'),
            'OPENAI_API_KEY' => env('OPENAI_API_KEY'),
        ];
    }

    public function computeScheduling($recurrence)
    {
        $process = new Process(
            ['python3', 'compute_scheduling.py', $recurrence],
            null, $this->env
        );
        $process->setWorkingDirectory('../python');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $output = trim($output);
        $output = json_decode($output);
        return $output;
    }

    public function computeVerifications(
        $conditions, $orchestratorConnections
    )
    {
        $verifications = array();
        foreach($orchestratorConnections as $orchestratorConnection) {
            $orchestratorConnectionId = $orchestratorConnection['orchestrator_connection']['id'];
            $tenantsOutput = array();
            foreach($orchestratorConnection['orchestrator_connection']['tenants'] as $tenant) {
                $tenantId = $tenant['id'];

                $releasesByFolder = array();
                foreach ($tenant['selected_releases'] as $release) {
                    $folder = $release['folder'];
                    $id = $release['id'];
                    if (!array_key_exists($folder, $releasesByFolder)) {
                        $releasesByFolder[$folder] = array();

                    }
                    array_push($releasesByFolder[$folder], $id);
                }
                
                $machinesByFolder = array();
                foreach ($tenant['selected_machines'] as $machine) {
                    $folder = $machine['folder'];
                    $id = $machine['id'];
                    if (!array_key_exists($folder, $machinesByFolder)) {
                        $machinesByFolder[$folder] = array();

                    }
                    array_push($machinesByFolder[$folder], $id);
                }

                $queuesByFolder = array();
                foreach ($tenant['selected_queues'] as $queue) {
                    $folder = $queue['folder'];
                    $id = $queue['id'];
                    if (!array_key_exists($folder, $queuesByFolder)) {
                        $queuesByFolder[$folder] = array();

                    }
                    array_push($queuesByFolder[$folder], $id);
                }

                $output = $this->computeVerificationsForTenant($conditions, $orchestratorConnectionId, $tenantId, $releasesByFolder, $machinesByFolder, $queuesByFolder);
                $tenantsOutput[$tenantId] = $output;
            }
            array_push($verifications, [
                'orchestrator_connection' => $orchestratorConnection['orchestrator_connection'],
                'tenants' => $tenantsOutput,
            ]);
        }

        return $verifications;
    }

    public function computeVerificationsForTenant(
        $conditions, $orchestratorConnectionId, $tenantId,
        $releasesByFolder, $machinesByFolder, $queuesByFolder,
    )
    {
        $uniqid = uniqid();
        $robotsLogsCSV = "data/robots-logs-$uniqid.csv";
        $robotsLogsJSON = "data/robots-logs-$uniqid.json";
        $jobsCSV = "data/jobs-$uniqid.csv";
        $jobsJSON = "data/jobs-$uniqid.json";
        $machinesCSV = "data/machines-$uniqid.csv";
        $machinesJSON = "data/machines-$uniqid.json";
        $queueItemsCSV = "data/queue-items-$uniqid.csv";
        $queueItemsJSON = "data/queue-items-$uniqid.json";

        $orchestratorConnectionModel = OrchestratorConnection::find($orchestratorConnectionId);
        $tenantModel = OrchestratorConnectionTenant::find($tenantId);

        foreach ($releasesByFolder as $folder => $releases) {
            $jobs = $this->orchestratorService->getJobs(
                $orchestratorConnectionModel,
                $tenantModel,
                $folder,
                $releases,
            );
            if ($jobs['ok'] && count($jobs['jobs'])) {
                Storage::disk('python')->put($jobsJSON, json_encode($jobs['jobs']));
                $json = new Json(Storage::disk('python')->path($jobsJSON));
                // To set a conversion option then convert JSON to CSV and save
                $json->setConversionKey('utf8_encoding', true);
                $json->convertAndSave(Storage::disk('python')->path($jobsCSV));

                $jobsKeys = array();
                foreach ($jobs['jobs'] as $job) {
                    array_push($jobsKeys, $job['Key']);
                }
                $robotsLogs = $this->orchestratorService->getLogs(
                    $orchestratorConnectionModel,
                    $tenantModel,
                    $folder,
                    $jobsKeys,
                );
                if ($robotsLogs['ok'] && count($robotsLogs['logs']) > 0) {
                    Storage::disk('python')->put($robotsLogsJSON, json_encode($robotsLogs['logs']));
                    $json = new Json(Storage::disk('python')->path($robotsLogsJSON));
                    // To set a conversion option then convert JSON to CSV and save
                    $json->setConversionKey('utf8_encoding', true);
                    $json->convertAndSave(Storage::disk('python')->path($robotsLogsCSV));
                }
            }
        }

        foreach ($machinesByFolder as $folder => $machines) {
            $machineSessionRuntimes = $this->orchestratorService->getMachineSessionRuntimes(
                $orchestratorConnectionModel,
                $tenantModel,
                $folder,
                $machines,
            );
            if ($machineSessionRuntimes['ok'] && count($machineSessionRuntimes['machine_session_runtimes']) > 0) {
                Storage::disk('python')->put($machinesJSON, json_encode($machineSessionRuntimes['machine_session_runtimes']));
                $json = new Json(Storage::disk('python')->path($machinesJSON));
                // To set a conversion option then convert JSON to CSV and save
                $json->setConversionKey('utf8_encoding', true);
                $json->convertAndSave(Storage::disk('python')->path($machinesCSV));
            }
        }

        foreach ($queuesByFolder as $folder => $queues) {
            $queueItems = $this->orchestratorService->getQueueItems(
                $orchestratorConnectionModel,
                $tenantModel,
                $folder,
                $queues,
            );
            if ($queueItems['ok'] && count($queueItems['queue_items']) > 0) {
                Storage::disk('python')->put($queueItemsJSON, json_encode($queueItems['queue_items']));
                $json = new Json(Storage::disk('python')->path($queueItemsJSON));
                // To set a conversion option then convert JSON to CSV and save
                $json->setConversionKey('utf8_encoding', true);
                $json->convertAndSave(Storage::disk('python')->path($queueItemsCSV));
            }
        }

        $process = new Process(
            ['python3', 'compute_verification.py', $conditions, $robotsLogsCSV, $jobsCSV, $machinesCSV, $queueItemsCSV],
            null, $this->env
        );
        $basePath = base_path();
        $process->setWorkingDirectory("{$basePath}/python");
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        Storage::disk('python')->delete([
            $robotsLogsCSV, $jobsCSV, $machinesCSV, $queueItemsCSV,
            $robotsLogsJSON, $jobsJSON, $machinesJSON, $queueItemsJSON,
        ]);

        $output = $process->getOutput();
        $output = trim($output);
        $output = json_decode($output);

        foreach ($output->verifications as $verification) {
            $dataSource = $verification->data_source;
            if ($dataSource !== -1) {
                if ($dataSource === 'Jobs execution history' || $dataSource === 'Robots logs') {
                    if (!property_exists($verification->answer, 'error')) {
                        foreach ($verification->answer->sources as $key => $source) {
                            $URL = $this->orchestratorService->generateJobURL(
                                $source,
                                $orchestratorConnectionModel,
                                $tenantModel,
                            );
                            $verification->answer->sources[$key] = [ 'id' => $source, 'url' => $URL ];
                        }
                    }
                } elseif ($dataSource === 'Queue items details') {
                    if (!property_exists($verification->answer, 'error')) {
                        foreach ($verification->answer->sources as $key => $source) {
                            $URL = $this->orchestratorService->generateQueueDefinitionURL(
                                $source,
                                $orchestratorConnectionModel,
                                $tenantModel,
                            );
                            $verification->answer->sources[$key] = [ 'id' => $source, 'url' => $URL ];
                        }
                    }
                } elseif ($dataSource === 'Machines details') {
                    if (!property_exists($verification->answer, 'error')) {
                        foreach ($verification->answer->sources as $key => $source) {
                            $machineSessionRuntime = $this->orchestratorService->getMachineSessionRuntime($source, $orchestratorConnectionModel, $tenantModel);
                            if ($machineSessionRuntime['ok'] && count($machineSessionRuntime['machine_session_runtime']) > 0) {
                                $hostMachineName = $machineSessionRuntime['machine_session_runtime'][0]['HostMachineName'];
                                $verification->answer->sources[$key] = [ 'id' => $source, 'host_machine_name' => $hostMachineName ];
                            } else {
                                $verification->answer->sources[$key] = [ 'id' => $source, 'host_machine_name' => __('Unknown') ];
                            }
                        }
                    }
                }
            }
        }

        return $output;
    }

    public function computeRecommendedActions($alert)
    {
        return json_decode('{ "results": ["Restart the affected UiPath process", "Update the input arguments in the Orchestrator"]}');
    }
}