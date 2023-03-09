<?php

namespace App\Services;

use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use GuzzleHttp\Psr7;
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

    function getAlerts(
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

    function createWebhook(
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

    function removeWebhook(
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
}
