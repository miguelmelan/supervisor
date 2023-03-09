<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrchestratorConnectionResource;
use App\Http\Resources\OrchestratorConnectionTenantResource;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Services\UiPathOrchestratorService;

class UiPathController extends Controller
{
    protected UiPathOrchestratorService $service;

    public function __construct(UiPathOrchestratorService $service)
    {
        $this->service = $service;
    }

    private function addVerificationStatusMessage($result, $extraInformation)
    {
        if ($result['ok']) {
            $result['message'] = [
                'verify' => __('Connection verified successfully.'),
                'extraInformation' => $extraInformation,
            ];
        } else {
            $result['message'] = [
                'verify' => $result['error'] ? __('Connection verification failed: :error.', [
                    'error' => $result['error']
                ]) : __('Connection verification failed.'),
                'extraInformation' => $extraInformation,
                'tenantsResults' => isset($result['tenants_results']) ? $result['tenants_results'] : null,
            ];
        }

        return $result;
    }

    public function folders(
        OrchestratorConnection $orchestratorConnection,
        OrchestratorConnectionTenant $orchestratorConnectionTenant,
        $withReleases,
        $withMachines,
        $withQueueDefinitions
    ) {
        $withReleases = filter_var($withReleases, FILTER_VALIDATE_BOOLEAN);
        $withMachines = filter_var($withMachines, FILTER_VALIDATE_BOOLEAN);
        $withQueueDefinitions = filter_var($withQueueDefinitions, FILTER_VALIDATE_BOOLEAN);
        $result = $this->service->getFoldersWithEntities(
            $orchestratorConnection,
            $orchestratorConnectionTenant,
            $withReleases,
            $withMachines,
            $withQueueDefinitions
        );

        // refresh data because there is a verification of credentials when getting folders
        $orchestratorConnection = OrchestratorConnection::find($orchestratorConnection->id);
        $orchestratorConnectionTenant = OrchestratorConnectionTenant::find($orchestratorConnectionTenant->id);
        $result['orchestrator_connection'] = new OrchestratorConnectionResource($orchestratorConnection);
        $result['orchestrator_connection_tenant'] = new OrchestratorConnectionTenantResource($orchestratorConnectionTenant);

        $extraInformation = [
            'verified' => $orchestratorConnection->verified,
            'verified_at_for_humans' => $orchestratorConnection->verified_at_for_humans,
        ];
        if ($orchestratorConnection->hosting_type === 'on_premise') {
            $extraInformation['tenantsVerificationStatuses'] = $orchestratorConnection->tenants->map->only([
                'id', 'verified', 'verified_at_for_humans'
            ])->toArray();
        }

        $result = $this->addVerificationStatusMessage($result, $extraInformation);

        // unnecessary properties removed
        unset($result['date']);
        unset($result['error']);
        unset($result['tenants_results']);
        return $result;
    }
}
