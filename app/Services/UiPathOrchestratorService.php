<?php

namespace App\Services;

use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantRelease;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class UiPathOrchestratorService
{
    private function authenticate($endpoint, $clientID, $clientSecret, $returnAccessToken = false)
    {
        $date = null;
        $ok = false;
        $error = null;
        $token = null;
        try {
            $date = Carbon::now();
            $response = Http::timeout(config('constants.uipath.orchestrator.identity_server_token_endpoint.timeout'))->withoutVerifying()->asForm()->post($endpoint, [
                'grant_type' => 'client_credentials',
                'client_id' => $clientID,
                'client_secret' => $clientSecret,
                'scope' => config('constants.uipath.orchestrator.identity_server_token_endpoint.default_scopes')
            ]);
            $ok = $response->json('access_token') !== null;
            $error = $ok ? '' : $response->json('error') ?? __('Server error');
            $token = $response->json('access_token');
        } catch (ConnectionException $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $result = [
            'date' => $date,
            'ok' => $ok,
            'error' => $error,
        ];
        if ($returnAccessToken) {
            $result['access_token'] = $token;
        }

        return $result;
    }

    private function authenticateWithOrchestratorConnection(OrchestratorConnection $orchestratorConnection)
    {
        $endpoint = $orchestratorConnection->identity_server_token_endpoint;
        $clientID = $orchestratorConnection->client_id;
        $clientSecret = Crypt::decryptString($orchestratorConnection->client_secret);

        return $this->authenticate($endpoint, $clientID, $clientSecret, true);
    }

    private function authenticateWithOrchestratorConnectionTenant(OrchestratorConnectionTenant $tenant)
    {
        $endpoint = $tenant->orchestratorConnection->identity_server_token_endpoint;
        $clientID = $tenant->client_id;
        $clientSecret = Crypt::decryptString($tenant->client_secret);

        return $this->authenticate($endpoint, $clientID, $clientSecret, true);
    }

    private function computeTenantsResults($result)
    {
        $date = Carbon::now();
        $computedResult = true;
        foreach ($result as $tenantResult) {
            $computedResult = $computedResult && $tenantResult['ok'];
            if (!$computedResult) {
                break;
            }
        }
        
        return [
            'date' => $date,
            'ok' => $computedResult,
            'error' => $computedResult ? '' : __('some of your tenants connection details cannot be verified'),
            'tenants_results' => $result,
        ];
    }

    public function checkCredentials($endpoint, $clientID, $clientSecret)
    {
        return $this->authenticate($endpoint, $clientID, $clientSecret);
    }

    public function checkTenantsCredentials($endpoint, $tenants)
    {
        $result = [];
        foreach ($tenants as $index => $tenant) {
            $id = $tenant['id'] ?? $index;
            $clientID = $tenant['client_id'];
            $clientSecret = $tenant['client_secret'];
            $tenantResult = $this->checkCredentials($endpoint, $clientID, $clientSecret);
            $tenantResult['id'] = $id;
            array_push($result, $tenantResult);
        }
        
        return $this->computeTenantsResults($result);
    }

    public function checkCredentialsWithOrchestratorConnection(OrchestratorConnection $orchestratorConnection)
    {
        $hostingType = $orchestratorConnection->hosting_type;
        $endpoint = $orchestratorConnection->identity_server_token_endpoint;

        if ($hostingType === 'cloud') {
            $clientID = $orchestratorConnection->client_id;
            $clientSecret = Crypt::decryptString($orchestratorConnection->client_secret);
            $result = $this->checkCredentials($endpoint, $clientID, $clientSecret);

            $orchestratorConnection->verified = $result['ok'];
            $orchestratorConnection->verified_at = $result['date'];
            $orchestratorConnection->save();
            foreach ($orchestratorConnection->tenants as $tenant) {
                $tenant->verified = $result['ok'];
                $tenant->verified_at = $result['date'];
                $tenant->save();
            }
        } elseif ($hostingType === 'on_premise') {
            $tenants = $orchestratorConnection->tenants;
            $tenantsAsArray = [];
            foreach ($tenants->toArray() as $tenant) {
                $tenant['client_secret'] = Crypt::decryptString($tenant['client_secret']);
                array_push($tenantsAsArray, $tenant);
            }
            $result = $this->checkTenantsCredentials($endpoint, $tenantsAsArray);

            $orchestratorConnection->verified = $result['ok'];
            $orchestratorConnection->verified_at = $result['date'];
            $orchestratorConnection->save();

            foreach ($tenants as $tenant) {
                $tenantResult = $result['tenants_results'][array_search($tenant->id, array_column($tenantsAsArray, 'id'))];
                $tenant->verified = $tenantResult['ok'];
                $tenant->verified_at = $tenantResult['date'];
                $tenant->save();
            }
        }

        return $result;
    }

    public function getFoldersWithEntities(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
        $withReleases,
        $withMachines,
        $withQueueDefinitions)
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $foldersWithEntities = [];
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'folders' => '',
                    'releases' => '',
                    'machines' => '',
                    'queue_definitions' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['folders'] = sprintf(config("$endpointSuffixBaseProperty.folders.all"), $tenantName);
                    $endpoints['releases'] = sprintf(config("$endpointSuffixBaseProperty.releases.all"), $tenantName);
                    $endpoints['queue_definitions'] = sprintf(config("$endpointSuffixBaseProperty.queue_definitions.all"), $tenantName);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['folders'] = config("$endpointSuffixBaseProperty.folders.all");
                    $endpoints['releases'] = config("$endpointSuffixBaseProperty.releases.all");
                    $endpoints['queue_definitions'] = config("$endpointSuffixBaseProperty.queue_definitions.all");
                }
    
                // getting folders
                $endpoint = $orchestratorConnection->url . $endpoints['folders'];
                $folders = Http::withoutVerifying()->withToken($token)->get($endpoint)->json('value');
                foreach ($folders as $folder) {
                    $id = $folder['Id'];
                    $folderWithEntities = [
                        'id' => $id,
                        'display_name' => $folder['DisplayName'],
                        'fully_qualified_name' => $folder['FullyQualifiedName'],
                        'parent_id' => $folder['ParentId']
                    ];
    
                    if ($withReleases) {
                        // getting releases (deployed processes)
                        $endpoint = $orchestratorConnection->url . $endpoints['releases'];
                        $releases = Http::withoutVerifying()->withToken($token)
                            ->acceptJson()
                            ->withHeaders([
                                'X-UIPATH-OrganizationUnitId' => $id
                            ])
                            ->get($endpoint)
                            ->json('value');
    
                        if ($releases && count($releases) > 0) {
                            $folderWithEntities['releases'] = array_map(function ($release) {
                                return [
                                    'id' => $release['Id'],
                                    'name' => $release['Name'],
                                    'description' => $release['Description'],
                                    'processKey' => $release['ProcessKey']
                                ];
                            }, $releases);
                        }
                    }
    
                    if ($withMachines) {
                        // getting machine session runtimes
                        if ($hostingType === 'cloud') {
                            $endpoints['machines'] = sprintf(config("$endpointSuffixBaseProperty.machines.all"), $tenantName, $id);
                        } elseif ($hostingType === 'on_premise') {
                            $endpoints['machines'] = sprintf(config("$endpointSuffixBaseProperty.machines.all"), $id);
                        }
                        $endpoint = $orchestratorConnection->url . $endpoints['machines'];
                        $machines = Http::withoutVerifying()->withToken($token)
                            ->acceptJson()
                            ->get($endpoint)
                            ->json('value');
    
                        if ($machines && count($machines) > 0) {
                            $folderWithEntities['machines'] = array_map(function ($machine) {
                                return [
                                    'id' => $machine['Id'],
                                    'name' => $machine['Name'],
                                    'description' => $machine['Description'],
                                    'type' => $machine['Type'],
                                    'non_production_slots' => $machine['NonProductionSlots'],
                                    'unattended_slots' => $machine['UnattendedSlots'],
                                    'headless_slots' => $machine['HeadlessSlots'],
                                    'test_automation_slots' => $machine['TestAutomationSlots'],
                                    'automation_cloud_slots' => $machine['AutomationCloudSlots'],
                                ];
                            }, $machines);
                        }
                    }
    
                    if ($withQueueDefinitions) {
                        // getting queue definitions
                        $endpoint = $orchestratorConnection->url . $endpoints['queue_definitions'];
                        $queueDefinitions = Http::withoutVerifying()->withToken($token)
                            ->acceptJson()
                            ->withHeaders([
                                'X-UIPATH-OrganizationUnitId' => $id
                            ])
                            ->get($endpoint)
                            ->json('value');
                            
                        if ($queueDefinitions && count($queueDefinitions) > 0) {
                            $folderWithEntities['queue_definitions'] = array_map(function ($queueDefinition) {
                                return [
                                    'id' => $queueDefinition['Id'],
                                    'name' => $queueDefinition['Name'],
                                    'description' => $queueDefinition['Description'],
                                    'accept_automatically_retry' => $queueDefinition['AcceptAutomaticallyRetry'],
                                    'max_number_of_retries' => $queueDefinition['MaxNumberOfRetries']
                                ];
                            }, $queueDefinitions);
                        }
                    }
    
                    array_push($foldersWithEntities, $folderWithEntities);
                }
    
                $result['folders'] = $foldersWithEntities;
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getAlerts(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant)
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'alerts' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['alerts'] = sprintf(config("$endpointSuffixBaseProperty.alerts.all"), $tenantName);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['alerts'] = config("$endpointSuffixBaseProperty.alerts.all");
                }
    
                // getting alerts
                $endpoint = $orchestratorConnection->url . $endpoints['alerts'];
                $result['alerts'] = Http::withoutVerifying()->withToken($token)->get($endpoint)->json('value');
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function createWebhook(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant)
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'webhooks' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['webhooks'] = sprintf(config("$endpointSuffixBaseProperty.webhooks.all"), $tenantName);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['webhooks'] = config("$endpointSuffixBaseProperty.webhooks.all");
                }
    
                // adding webhook
                $endpoint = $orchestratorConnection->url . $endpoints['webhooks'];
                $result['webhook'] = Http::withoutVerifying()
                    ->withToken($token)
                    ->post($endpoint, [
                        'Name' => 'supervisor-' . $tenant->uuid,
                        'Description' => 'Created by supervisor external application to trap alerts',
                        'Url' => route('configuration.orchestrator-connections.tenants.webhook-handler', [
                            'uuid' => $tenant->uuid,
                        ]),
                        'Enabled' => true,
                        'Secret' => Crypt::decryptString($tenant->webhook_secret),
                        'SubscribeToAllEvents' => false,
                        'AllowInsecureSsl' => true,
                        'Events' => [
                            [ 'EventType' => 'job.faulted' ],
                            [ 'EventType' => 'job.created' ],
                            [ 'EventType' => 'job.started' ],
                            [ 'EventType' => 'job.suspended' ],
                            [ 'EventType' => 'schedule.failed' ],
                            [ 'EventType' => 'queueItem.transactionFailed' ],
                            [ 'EventType' => 'queueItem.transactionStarted' ],
                            [ 'EventType' => 'robot.status' ],
                        ],
                    ])
                    ->json();
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function removeWebhook(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant)
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            $tenantWebhookId = $tenant->webhook_id;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'webhooks' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['webhooks'] = sprintf(config("$endpointSuffixBaseProperty.webhooks.remove"), $tenantName, $tenantWebhookId);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['webhooks'] = sprintf(config("$endpointSuffixBaseProperty.webhooks.remove"), $tenantWebhookId);
                }
    
                // removing webhook
                $endpoint = $orchestratorConnection->url . $endpoints['webhooks'];
                $result['ok'] = Http::withoutVerifying()
                    ->withToken($token)
                    ->delete($endpoint)
                    ->successful();
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getJobs(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
        $folder,
        $releases = [],
        $keys = [])
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'jobs' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['jobs'] = sprintf(config("$endpointSuffixBaseProperty.jobs.all"), $tenantName);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['jobs'] = sprintf(config("$endpointSuffixBaseProperty.jobs.all"));
                }
    
                // getting jobs
                $endpoint = $orchestratorConnection->url . $endpoints['jobs'];
                if (count($releases) > 0) {
                    $filter = implode(',', $releases);
                    $endpoint .= "?\$filter=Release/Id in ($filter)";
                } elseif (count($keys) > 0) {
                    $filter = implode(',', $keys);
                    $endpoint .= "?\$filter=Key in ($filter)";
                }
                $result['jobs'] = Http::withoutVerifying()->withToken($token)->withHeaders([
                    'X-UIPATH-OrganizationUnitId' => $folder,
                ])->get($endpoint)->json('value');
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getLogs(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
        $folder,
        $jobs = [])
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'logs' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['logs'] = sprintf(config("$endpointSuffixBaseProperty.logs.all"), $tenantName);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['logs'] = sprintf(config("$endpointSuffixBaseProperty.logs.all"));
                }
    
                // getting logs
                $endpoint = $orchestratorConnection->url . $endpoints['logs'];
                $endpoint .= "?\$orderby=TimeStamp asc";
                if (count($jobs) > 0) {
                    $filter = implode(',', $jobs);
                    $endpoint .= "&\$filter=JobKey in ($filter)";
                }
                $result['logs'] = Http::withoutVerifying()->withToken($token)->withHeaders([
                    'X-UIPATH-OrganizationUnitId' => $folder,
                ])->get($endpoint)->json('value');
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getMachineSessionRuntimes(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
        $folder,
        $machines = []
    )
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'machine_session_runtimes' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['machine_session_runtimes'] = sprintf(config("$endpointSuffixBaseProperty.machine_session_runtimes.all"), $tenantName, $folder);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['machine_session_runtimes'] = sprintf(config("$endpointSuffixBaseProperty.machine_session_runtimes.all"), $folder);
                }
    
                // getting machine session runtimes
                $endpoint = $orchestratorConnection->url . $endpoints['machine_session_runtimes'];
                if (count($machines) > 0) {
                    $filter = implode(',', $machines);
                    $endpoint .= "?\$filter=MachineId in ($filter)";
                }
                $result['machine_session_runtimes'] = Http::withoutVerifying()->withToken($token)->get($endpoint)->json('value');
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getQueueItems(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
        $folder,
        $queues = []
    )
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'queue_items' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['queue_items'] = sprintf(config("$endpointSuffixBaseProperty.queue_items.all"), $tenantName);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['queue_items'] = sprintf(config("$endpointSuffixBaseProperty.queue_items.all"));
                }
    
                // getting machine session runtimes
                $endpoint = $orchestratorConnection->url . $endpoints['queue_items'];
                if (count($queues) > 0) {
                    $filter = implode(',', $queues);
                    $endpoint .= "?\$filter=QueueDefinitionId in ($filter)";
                }
                $result['queue_items'] = Http::withoutVerifying()->withToken($token)->withHeaders([
                    'X-UIPATH-OrganizationUnitId' => $folder,
                ])->get($endpoint)->json('value');
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getMachineSessionRuntime($id, OrchestratorConnection $orchestratorConnection, OrchestratorConnectionTenant $tenant)
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'machines' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['machine_session_runtimes'] = sprintf(config("$endpointSuffixBaseProperty.machine_session_runtimes.single"), $tenantName, $id);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['machine_session_runtimes'] = sprintf(config("$endpointSuffixBaseProperty.machine_session_runtimes.single"), $id);
                }
    
                // getting machine session runtimes
                $endpoint = $orchestratorConnection->url . $endpoints['machine_session_runtimes'];
                $result['machine_session_runtime'] = Http::withoutVerifying()->withToken($token)->get($endpoint)->json('value');
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function getJob($id, OrchestratorConnection $orchestratorConnection, OrchestratorConnectionTenant $tenant)
    {
        $result = $this->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            $hostingType = $orchestratorConnection->hosting_type;
            $tenantName = $tenant->name;
            if ($hostingType === 'cloud') {
                $result = $this->authenticateWithOrchestratorConnection($orchestratorConnection);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->authenticateWithOrchestratorConnectionTenant($tenant);
            }
    
            if ($result['ok']) {
                $token = $result['access_token'];
                $endpoints = [
                    'jobs' => '',
                ];
                $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
                if ($hostingType === 'cloud') {
                    $endpoints['jobs'] = sprintf(config("$endpointSuffixBaseProperty.jobs.single"), $tenantName, $id);
                } elseif ($hostingType === 'on_premise') {
                    $endpoints['jobs'] = sprintf(config("$endpointSuffixBaseProperty.jobs.single"), $id);
                }
    
                // getting machine session runtimes
                $endpoint = $orchestratorConnection->url . $endpoints['jobs'];
                $result['job'] = Http::withoutVerifying()->withToken($token)->get($endpoint)->json();
            }
        }

        // !important: do not share access_token
        unset($result['access_token']);
        return $result;
    }

    public function generateJobURL(
        $id,
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
    ) {
        $hostingType = $orchestratorConnection->hosting_type;
        $tenantName = $tenant->name;
        $endpoints = [
            'jobs' => '',
        ];
        $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
        if ($hostingType === 'cloud') {
            $endpoints['jobs'] = sprintf(config("$endpointSuffixBaseProperty.jobs.url"), $tenantName, $id);
        } elseif ($hostingType === 'on_premise') {
            $endpoints['jobs'] = sprintf(config("$endpointSuffixBaseProperty.jobs.url"), $id);
        }
        $endpoint = $orchestratorConnection->url . $endpoints['jobs'];
        
        return $endpoint;
    }

    public function generateQueueDefinitionURL(
        $id,
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $tenant,
    ) {
        $hostingType = $orchestratorConnection->hosting_type;
        $tenantName = $tenant->name;
        $endpoints = [
            'queue_definitions' => '',
        ];
        $endpointSuffixBaseProperty = "constants.uipath.orchestrator.api_endpoint.suffixes.$hostingType";
        if ($hostingType === 'cloud') {
            $endpoints['queue_definitions'] = sprintf(config("$endpointSuffixBaseProperty.queue_definitions.url"), $tenantName, $id);
        } elseif ($hostingType === 'on_premise') {
            $endpoints['queue_definitions'] = sprintf(config("$endpointSuffixBaseProperty.queue_definitions.url"), $id);
        }
        $endpoint = $orchestratorConnection->url . $endpoints['queue_definitions'];
        
        return $endpoint;
    }
}
