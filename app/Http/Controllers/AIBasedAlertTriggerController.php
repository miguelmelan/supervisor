<?php

namespace App\Http\Controllers;

use App\Events\AIBasedAlertTriggerCreated;
use App\Http\Requests\StoreAIBasedAlertTriggerRequest;
use App\Http\Requests\UpdateAIBasedAlertTriggerRequest;
use App\Http\Resources\AIBasedAlertTriggerResource;
use App\Http\Resources\OrchestratorConnectionResource;
use App\Models\AIBasedAlertTrigger;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantMachine;
use App\Models\OrchestratorConnectionTenantQueue;
use App\Models\OrchestratorConnectionTenantRelease;
use App\Services\PythonService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;

use Inertia\Inertia;

class AIBasedAlertTriggerController extends Controller
{
    protected PythonService $python;

    public function __construct(PythonService $service)
    {
        $this->python = $service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        request()->validate([
            'sorting.direction' => ['in:asc,desc'],
            'sorting.field' => ['in:code,name'],
        ]);

        $alertTriggers = AIBasedAlertTrigger::query()
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(request('sorting'), function ($query, $sorting) {
                $query->orderBy($sorting['field'], $sorting['direction']);
            })
            ->when(!request('sorting'), function ($query) {
                $query->orderBy('code');
            })
            ->paginate(config('constants.pagination.items_per_page'))
            ->withQueryString()
            ->through(fn ($alertTrigger) => [
                'id' => $alertTrigger->id,
                'code' => $alertTrigger->code,
                'name' => $alertTrigger->name,
            ]);

        return Inertia::render('Configuration/AIBasedAlertTrigger/Index', [
            'alertTriggers' => $alertTriggers,
            'filters' => Request::only(['search', 'sorting'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Configuration/AIBasedAlertTrigger/Create', [
            'orchestratorConnections' => OrchestratorConnectionResource::collection(
                OrchestratorConnection::all()->sortBy('code')
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAIBasedAlertTriggerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAIBasedAlertTriggerRequest $request)
    {
        $attributes = $request->validated();
        
        foreach ($attributes['verifications'] as $verification) {
            $verification['orchestrator_connection'] = new OrchestratorConnectionResource(
                OrchestratorConnection::find($verification['orchestrator_connection']['id'])
            );
        }
        
        $alertTrigger = AIBasedAlertTrigger::create($attributes);
        $alertTrigger->save();
        $alertTrigger->attachTags(array_column($attributes['tags'], 'name'));

        foreach ($attributes['orchestrator_connections'] as $orchestratorConnectionConfiguration) {
            $connection = $orchestratorConnectionConfiguration['orchestrator_connection'];

            $alertTrigger->orchestratorConnections()
                ->attach($connection['id']);

            foreach ($orchestratorConnectionConfiguration['tenants'] as $tenant) {
                $alertTrigger->orchestratorConnectionTenants()
                    ->attach($tenant);
            }

            foreach ($connection['tenants'] as $tenant) {
                if (in_array($tenant['id'], $orchestratorConnectionConfiguration['tenants'])) {
                    $releases = $tenant['selected_releases'];
                    $machines = $tenant['selected_machines'];
                    $queues = $tenant['selected_queues'];
                    $tenantModel = OrchestratorConnectionTenant::find($tenant['id']);

                    foreach ($releases as $release) {
                        $release = OrchestratorConnectionTenantRelease::where('external_id', $release['id'])
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $release) {
                                return $tenantModel->releases()->create([
                                    'external_id' => $release['id'],
                                    'external_folder_id' => $release['folder'],
                                ]);
                            });
                        $alertTrigger->releases()->attach($release->id);
                    }

                    foreach ($machines as $machine) {
                        $machine = OrchestratorConnectionTenantMachine::where('external_id', $machine['id'])
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $machine) {
                                return $tenantModel->machines()->create([
                                    'external_id' => $machine['id'],
                                    'external_folder_id' => $machine['folder'],
                                ]);
                            });
                        $alertTrigger->machines()->attach($machine->id);
                    }

                    foreach ($queues as $queue) {
                        $queue = OrchestratorConnectionTenantQueue::where('external_id', $queue['id'])
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $queue) {
                                return $tenantModel->queues()->create([
                                    'external_id' => $queue['id'],
                                    'external_folder_id' => $queue['folder'],
                                ]);
                            });
                        $alertTrigger->queues()->attach($queue->id);
                    }
                }
            }
        }

        session()->flash('flash.banner', __('AI based alert trigger named :name successfully defined!', [
            'name' => $attributes['name']
        ]));
        session()->flash('flash.bannerStyle', 'success');

        AIBasedAlertTriggerCreated::dispatch($alertTrigger);

        return redirect()->route('configuration.ai-based-alert-triggers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AIBasedAlertTrigger  $aiBasedAlertTrigger
     * @return \Illuminate\Http\Response
     */
    public function show(AIBasedAlertTrigger $aiBasedAlertTrigger)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AIBasedAlertTrigger  $aiBasedAlertTrigger
     * @return \Illuminate\Http\Response
     */
    public function edit(AIBasedAlertTrigger $aiBasedAlertTrigger)
    {
        $aiBasedAlertTrigger = $aiBasedAlertTrigger->load('tags');
        $aiBasedAlertTrigger = $aiBasedAlertTrigger->load('releases');
        $aiBasedAlertTrigger = $aiBasedAlertTrigger->load('machines');
        $aiBasedAlertTrigger = $aiBasedAlertTrigger->load('queues');

        return Inertia::render('Configuration/AIBasedAlertTrigger/Edit', [
            'alertTrigger' => new AIBasedAlertTriggerResource($aiBasedAlertTrigger),
            'orchestratorConnections' => OrchestratorConnectionResource::collection(
                OrchestratorConnection::all()->sortBy('code')
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAIBasedAlertTriggerRequest  $request
     * @param  \App\Models\AIBasedAlertTrigger  $aiBasedAlertTrigger
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAIBasedAlertTriggerRequest $request, AIBasedAlertTrigger $aiBasedAlertTrigger)
    {
        $attributes = $request->validated();

        $aiBasedAlertTrigger = AIBasedAlertTrigger::find($aiBasedAlertTrigger->id);
        
        foreach ($attributes['verifications'] as $key => $verification) {
            $attributes['verifications'][$key]['orchestrator_connection'] = new OrchestratorConnectionResource(
                OrchestratorConnection::find($verification['orchestrator_connection']['id'])
            );
        }

        $aiBasedAlertTrigger->fill($attributes);
        $aiBasedAlertTrigger->save();

        $aiBasedAlertTrigger->syncTags(array_column($attributes['tags'], 'name'));

        $orchestratorConnectionsToSync = [];
        $orchestratorConnectionsTenantsToSync = [];
        $releasesToSync = [];
        $machinesToSync = [];
        $queuesToSync = [];

        foreach ($attributes['orchestrator_connections'] as $orchestratorConnectionConfiguration) {
            $connection = $orchestratorConnectionConfiguration['orchestrator_connection'];
            array_push($orchestratorConnectionsToSync, $connection['id']);

            foreach ($connection['tenants'] as $tenant) {
                if (in_array($tenant['id'], $orchestratorConnectionConfiguration['tenants'])) {
                    $selectedReleases = $tenant['selected_releases'];
                    $selectedMachines = $tenant['selected_machines'];
                    $selectedQueues = $tenant['selected_queues'];
                    $tenantModel = OrchestratorConnectionTenant::find($tenant['id']);

                    array_push($orchestratorConnectionsTenantsToSync, $tenant['id']);

                    $selectedReleasesIds = array();
                    foreach ($selectedReleases as $release) {
                        $releaseId = $release['id'];
                        array_push($selectedReleasesIds, $releaseId);
                        OrchestratorConnectionTenantRelease::where('external_id', $releaseId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $releaseId) {
                                return $tenantModel->releases()->create([
                                    'external_id' => $releaseId,
                                ]);
                            });
                    }

                    $selectedMachinesIds = array();
                    foreach ($selectedMachines as $machine) {
                        $machineId = $machine['id'];
                        array_push($selectedMachinesIds, $machineId);
                        OrchestratorConnectionTenantMachine::where('external_id', $machineId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $machineId) {
                                return $tenantModel->machines()->create([
                                    'external_id' => $machineId,
                                ]);
                            });
                    }

                    $selectedQueuesIds = array();
                    foreach ($selectedQueues as $queue) {
                        $queueId = $queue['id'];
                        array_push($selectedQueuesIds, $queueId);
                        OrchestratorConnectionTenantQueue::where('external_id', $queueId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $queueId) {
                                return $tenantModel->queues()->create([
                                    'external_id' => $queueId,
                                ]);
                            });
                    }

                    $releases = OrchestratorConnectionTenantRelease::where('tenant_id', $tenant['id'])
                        ->whereIn('external_id', $selectedReleasesIds)->get()->pluck('id')->toArray();
                    $releasesToSync = array_merge($releasesToSync, $releases);

                    $machines = OrchestratorConnectionTenantMachine::where('tenant_id', $tenant['id'])
                        ->whereIn('external_id', $selectedMachinesIds)->get()->pluck('id')->toArray();
                    $machinesToSync = array_merge($machinesToSync, $machines);

                    $queues = OrchestratorConnectionTenantQueue::where('tenant_id', $tenant['id'])
                        ->whereIn('external_id', $selectedQueuesIds)->get()->pluck('id')->toArray();
                    $queuesToSync = array_merge($queuesToSync, $queues);
                }
            }
        }

        $aiBasedAlertTrigger->orchestratorConnections()
            ->sync($orchestratorConnectionsToSync);

        $aiBasedAlertTrigger->orchestratorConnectionTenants()
            ->sync($orchestratorConnectionsTenantsToSync);

        $aiBasedAlertTrigger->releases()->sync($releasesToSync);
        $aiBasedAlertTrigger->machines()->sync($machinesToSync);
        $aiBasedAlertTrigger->queues()->sync($queuesToSync);

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AIBasedAlertTrigger  $aiBasedAlertTrigger
     * @return \Illuminate\Http\Response
     */
    public function destroy(AIBasedAlertTrigger $aiBasedAlertTrigger)
    {
        $name = $aiBasedAlertTrigger->name;
        $aiBasedAlertTrigger->delete();
        sleep(1);

        session()->flash('flash.banner', __('AI based alert trigger named :name successfully removed!', [
            'name' => $name
        ]));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('configuration.ai-based-alert-triggers.index');
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(HttpRequest $request)
    {
        $selected = $request->selected;
        AIBasedAlertTrigger::destroy($selected);
        sleep(1);

        session()->flash('flash.banner', __('AI based alert triggers successfully removed!'));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('configuration.ai-based-alert-triggers.index');
    }

    public function checkConditions(HttpRequest $request)
    {
        $conditions = $request->conditions;
        $orchestratorConnections = $request->orchestrator_connections;
        $verifications = $this->python->computeVerifications($conditions, $orchestratorConnections);
        session()->flash('message', [
            'verifications' => $verifications,
        ]);
        return back(303);
    }

    public function computeScheduling(HttpRequest $request)
    {
        $recurrence = $request->recurrence;
        $scheduling = $this->python->computeScheduling($recurrence);
        session()->flash('message', [
            'scheduling' => $scheduling,
        ]);
        return back(303);
    }
}
