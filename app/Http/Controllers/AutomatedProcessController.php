<?php

namespace App\Http\Controllers;

use App\Events\AutomatedProcessCreated;
use App\Http\Requests\StoreAutomatedProcessRequest;
use App\Http\Requests\UpdateAutomatedProcessRequest;
use App\Http\Resources\AutomatedProcessResource;
use App\Http\Resources\OrchestratorConnectionResource;
use App\Models\AutomatedProcess;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantMachine;
use App\Models\OrchestratorConnectionTenantQueue;
use App\Models\OrchestratorConnectionTenantRelease;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;

use Inertia\Inertia;

class AutomatedProcessController extends Controller
{
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

        $automatedProcesses = AutomatedProcess::query()
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
            ->through(fn ($automatedProcess) => [
                'id' => $automatedProcess->id,
                'code' => $automatedProcess->code,
                'name' => $automatedProcess->name,
            ]);

        return Inertia::render('Configuration/AutomatedProcess/Index', [
            'automatedProcesses' => $automatedProcesses,
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
        return Inertia::render('Configuration/AutomatedProcess/Create', [
            'orchestratorConnections' => OrchestratorConnectionResource::collection(
                OrchestratorConnection::all()->sortBy('code')
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAutomatedProcessRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAutomatedProcessRequest $request)
    {
        $attributes = $request->validated();
        //dd($attributes);
        $automatedProcess = AutomatedProcess::create($attributes);
        $automatedProcess->save();
        $automatedProcess->attachTags(array_column($attributes['tags'], 'name'));

        foreach ($attributes['orchestrator_connections'] as $orchestratorConnectionConfiguration) {
            $connection = $orchestratorConnectionConfiguration['orchestrator_connection'];
            $pivot = [
                'built_in_alerts' => $connection['pivot']['built_in_alerts'],
                'processes_supervision' => $connection['pivot']['processes_supervision'],
                'machines_supervision' => $connection['pivot']['machines_supervision'],
                'queues_supervision' => $connection['pivot']['queues_supervision'],
                'kibana_built_in_alerts' => $connection['pivot']['kibana_built_in_alerts'],
            ];

            $automatedProcess->orchestratorConnections()
                ->attach($connection['id'], $pivot);

            foreach ($orchestratorConnectionConfiguration['tenants'] as $tenant) {
                $tenantPivot = null;
                foreach ($connection['tenants'] as $model) {
                    if ($model['id'] === $tenant) {
                        $tenantPivot = $model['pivot'];
                        break;
                    }
                }
                $automatedProcess->orchestratorConnectionTenants()
                    ->attach($tenant, $tenantPivot);
            }

            foreach ($connection['tenants'] as $tenant) {
                if (in_array($tenant['id'], $orchestratorConnectionConfiguration['tenants'])) {
                    $releases = $tenant['selected_releases'];
                    $machines = $tenant['selected_machines'];
                    $queues = $tenant['selected_queues'];
                    $tenantModel = OrchestratorConnectionTenant::find($tenant['id']);

                    foreach ($releases as $releaseId) {
                        $release = OrchestratorConnectionTenantRelease::where('external_id', $releaseId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $releaseId) {
                                return $tenantModel->releases()->create([
                                    'external_id' => $releaseId,
                                ]);
                            });
                        $automatedProcess->releases()->attach($release->id);
                    }

                    foreach ($machines as $machineId) {
                        $machine = OrchestratorConnectionTenantMachine::where('external_id', $machineId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $machineId) {
                                return $tenantModel->machines()->create([
                                    'external_id' => $machineId,
                                ]);
                            });
                        $automatedProcess->machines()->attach($machine->id);
                    }

                    foreach ($queues as $queueId) {
                        $queue = OrchestratorConnectionTenantQueue::where('external_id', $queueId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $queueId) {
                                return $tenantModel->queues()->create([
                                    'external_id' => $queueId,
                                ]);
                            });
                        $automatedProcess->queues()->attach($queue->id);
                    }
                }
            }
        }

        session()->flash('flash.banner', __('Business automated process named :name successfully defined!', [
            'name' => $attributes['name']
        ]));
        session()->flash('flash.bannerStyle', 'success');

        AutomatedProcessCreated::dispatch($automatedProcess);

        return redirect()->route('configuration.automated-processes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AutomatedProcess  $automatedProcess
     * @return \Illuminate\Http\Response
     */
    public function show(AutomatedProcess $automatedProcess)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AutomatedProcess  $automatedProcess
     * @return \Illuminate\Http\Response
     */
    public function edit(AutomatedProcess $automatedProcess)
    {
        $automatedProcess = $automatedProcess->load('tags');
        $automatedProcess = $automatedProcess->load('releases');
        $automatedProcess = $automatedProcess->load('machines');
        $automatedProcess = $automatedProcess->load('queues');

        return Inertia::render('Configuration/AutomatedProcess/Edit', [
            'automatedProcess' => new AutomatedProcessResource($automatedProcess),
            'orchestratorConnections' => OrchestratorConnectionResource::collection(
                OrchestratorConnection::all()->sortBy('code')
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAutomatedProcessRequest  $request
     * @param  \App\Models\AutomatedProcess  $automatedProcess
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAutomatedProcessRequest $request, AutomatedProcess $automatedProcess)
    {
        $attributes = $request->validated();
        $automatedProcess = AutomatedProcess::find($automatedProcess->id);
        $automatedProcess->fill($attributes);
        $automatedProcess->save();

        $automatedProcess->syncTags(array_column($attributes['tags'], 'name'));

        $orchestratorConnectionsToSync = [];
        $orchestratorConnectionsTenantsToSync = [];
        $releasesToSync = [];
        $machinesToSync = [];
        $queuesToSync = [];

        foreach ($attributes['orchestrator_connections'] as $orchestratorConnectionConfiguration) {
            $connection = $orchestratorConnectionConfiguration['orchestrator_connection'];
            $pivot = $connection['pivot'];

            $orchestratorConnectionsToSync[$connection['id']] = $pivot;

            foreach ($connection['tenants'] as $tenant) {
                if (in_array($tenant['id'], $orchestratorConnectionConfiguration['tenants'])) {
                    $pivot = $tenant['pivot'];
                    $selectedReleases = $tenant['selected_releases'];
                    $selectedMachines = $tenant['selected_machines'];
                    $selectedQueues = $tenant['selected_queues'];
                    $tenantModel = OrchestratorConnectionTenant::find($tenant['id']);

                    $orchestratorConnectionsTenantsToSync[$tenant['id']] = $pivot;

                    foreach ($selectedReleases as $releaseId) {
                        OrchestratorConnectionTenantRelease::where('external_id', $releaseId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $releaseId) {
                                return $tenantModel->releases()->create([
                                    'external_id' => $releaseId,
                                ]);
                            });
                    }
                    foreach ($selectedMachines as $machineId) {
                        OrchestratorConnectionTenantMachine::where('external_id', $machineId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $machineId) {
                                return $tenantModel->machines()->create([
                                    'external_id' => $machineId,
                                ]);
                            });
                    }
                    foreach ($selectedQueues as $queueId) {
                        OrchestratorConnectionTenantQueue::where('external_id', $queueId)
                            ->where('tenant_id', $tenant['id'])
                            ->firstOr(function () use ($tenantModel, $queueId) {
                                return $tenantModel->queues()->create([
                                    'external_id' => $queueId,
                                ]);
                            });
                    }

                    $releases = OrchestratorConnectionTenantRelease::where('tenant_id', $tenant['id'])
                        ->whereIn('external_id', $selectedReleases)->get()->pluck('id')->toArray();
                    $releasesToSync = array_merge($releasesToSync, $releases);

                    $machines = OrchestratorConnectionTenantMachine::where('tenant_id', $tenant['id'])
                        ->whereIn('external_id', $selectedMachines)->get()->pluck('id')->toArray();
                    $machinesToSync = array_merge($machinesToSync, $machines);

                    $queues = OrchestratorConnectionTenantQueue::where('tenant_id', $tenant['id'])
                        ->whereIn('external_id', $selectedQueues)->get()->pluck('id')->toArray();
                    $queuesToSync = array_merge($queuesToSync, $queues);
                }
            }
        }

        $automatedProcess->orchestratorConnections()
            ->sync($orchestratorConnectionsToSync);

        $automatedProcess->orchestratorConnectionTenants()
            ->sync($orchestratorConnectionsTenantsToSync);

        $automatedProcess->releases()->sync($releasesToSync);
        $automatedProcess->machines()->sync($machinesToSync);
        $automatedProcess->queues()->sync($queuesToSync);

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AutomatedProcess  $automatedProcess
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutomatedProcess $automatedProcess)
    {
        $name = $automatedProcess->name;
        $automatedProcess->delete();
        sleep(1);

        session()->flash('flash.banner', __('Business automated process named :name successfully removed!', [
            'name' => $name
        ]));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('configuration.automated-processes.index');
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
        AutomatedProcess::destroy($selected);
        sleep(1);

        session()->flash('flash.banner', __('Business automated processes successfully removed!'));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('configuration.automated-processes.index');
    }
}
