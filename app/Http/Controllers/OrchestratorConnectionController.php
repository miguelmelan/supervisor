<?php

namespace App\Http\Controllers;

use App\Events\OrchestratorConnectionCreated;
use App\Http\Requests\StoreOrchestratorConnectionRequest;
use App\Http\Requests\UpdateOrchestratorConnectionRequest;
use App\Http\Resources\OrchestratorConnectionResource;
use App\Jobs\InterpretOrchestratorConnectionTenantWebhookEvent;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Services\UiPathOrchestratorService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class OrchestratorConnectionController extends Controller
{
    protected UiPathOrchestratorService $service;

    public function __construct(UiPathOrchestratorService $service)
    {
        $this->service = $service;
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
            'sorting.field' => ['in:code,name,hosting_type,environment_type'],
        ]);

        $orchestratorConnections = OrchestratorConnection::query()
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
            ->through(fn ($orchestratorConnection) => [
                'id' => $orchestratorConnection->id,
                'code' => $orchestratorConnection->code,
                'name' => $orchestratorConnection->name,
                'hosting_type' => $orchestratorConnection->hosting_type,
                'environment_type' => $orchestratorConnection->environment_type,
                'client_id' => $orchestratorConnection->client_id,
                'url' => $orchestratorConnection->url,
                'organization_name' => $orchestratorConnection->organization_name,
                'elasticsearch_enabled' => $orchestratorConnection->elasticsearch_enabled,
                'elasticsearch_index_configuration' => $orchestratorConnection->elasticsearch_index_configuration,
                'elasticsearch_url' => $orchestratorConnection->elasticsearch_url,
                'elasticsearch_username' => $orchestratorConnection->elasticsearch_username,
                'kibana_enabled' => $orchestratorConnection->kibana_enabled,
                'kibana_url' => $orchestratorConnection->kibana_url,
                'verified' => $orchestratorConnection->verified,
                'verified_at_for_humans' => $orchestratorConnection->verified_at_for_humans,
            ]);

        return Inertia::render('Configuration/OrchestratorConnection/Index', [
            'orchestratorConnections' => $orchestratorConnections,
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
        return Inertia::render('Configuration/OrchestratorConnection/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrchestratorConnectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrchestratorConnectionRequest $request)
    {
        $attributes = $request->validated();
        if (empty($attributes['elasticsearch_password'])) {
            unset($attributes['elasticsearch_password']);
        }
        $orchestratorConnection = OrchestratorConnection::create($attributes);
        $orchestratorConnection->save();
        $orchestratorConnection->tenants()->createMany($attributes['tenants']);
        $orchestratorConnection->attachTags(array_column($attributes['tags'], 'name'));
        $result = $this->service->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            foreach($orchestratorConnection->tenants() as $tenant) {
                $result = $this->service->createWebhook($orchestratorConnection, $tenant);
                if ($result['ok']) {
                    $tenant->webhook_id = $result['webhook']['Id'];
                    $tenant->save();
                }
            }
        }

        session()->flash('flash.banner', __('UiPath Orchestrator named :name successfully connected!', [
            'name' => $attributes['name']
        ]));
        session()->flash('flash.bannerStyle', 'success');

        OrchestratorConnectionCreated::dispatch($orchestratorConnection);

        return redirect()->route('configuration.orchestrator-connections.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrchestratorConnection  $orchestratorConnection
     * @return \Illuminate\Http\Response
     */
    public function edit(OrchestratorConnection $orchestratorConnection)
    {
        $orchestratorConnection = $orchestratorConnection->load('tags');
        return Inertia::render('Configuration/OrchestratorConnection/Edit', [
            'orchestratorConnection' => new OrchestratorConnectionResource($orchestratorConnection)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrchestratorConnectionRequest  $request
     * @param  \App\Models\OrchestratorConnection  $orchestratorConnection
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrchestratorConnectionRequest $request, OrchestratorConnection $orchestratorConnection)
    {
        $attributes = $request->validated();
        $orchestratorConnection = OrchestratorConnection::find($orchestratorConnection->id);
        if (empty($attributes['client_secret'])) {
            unset($attributes['client_secret']);
        }
        if (empty($attributes['elasticsearch_password'])) {
            unset($attributes['elasticsearch_password']);
        }
        $urlOrOrganizationChanged = (
            isset($attributes['url']) && $orchestratorConnection->url !== $attributes['url']) 
            || (isset($attributes['organization_name']) && $orchestratorConnection->organization_name !== $attributes['organization_name']
        );
        $orchestratorConnection->fill($attributes);
        $orchestratorConnection->save();

        foreach ($attributes['tenants'] as $key => $tenant) {
            $originalTenant = OrchestratorConnectionTenant::find($tenant['id']);

            // remove pivot attribute, not fillable by default
            unset($tenant['pivot']);
            // remove verified_at_for_humans attribute, not fillable by default
            unset($tenant['verified_at_for_humans']);

            if (empty($tenant['client_secret'])) {
                // get original client_secret from db
                $tenant['client_secret'] = $originalTenant->client_secret;
            } else {
                // StringencryptString client_secrent
                $tenant['client_secret'] = Crypt::encryptString($tenant['client_secret']);
            }
            $tenant['uuid'] = $originalTenant->uuid;
            $tenant['webhook_secret'] = $originalTenant->webhook_secret;
            $tenant['webhook_id'] = $originalTenant->webhook_id;
            // reassign attributes
            $attributes['tenants'][$key] = $tenant;
        }
        $orchestratorConnection->tenants()->createUpdateOrDelete($attributes['tenants']);
        $orchestratorConnection->syncTags(array_column($attributes['tags'], 'name'));
        
        $result = $this->service->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            foreach($orchestratorConnection->tenants as $tenant) {
                if ($tenant->webhook_id === null || $urlOrOrganizationChanged) {
                    if ($urlOrOrganizationChanged) {
                        $webhookRemoved = $this->service->removeWebhook($orchestratorConnection, $tenant);
                        if (!$webhookRemoved['ok']) {
                            // !important: notifier l'utilisateur
                        }
                    }
                    $webhookCreated = $this->service->createWebhook($orchestratorConnection, $tenant);
                    if ($webhookCreated['ok']) {
                        $tenant->webhook_id = $webhookCreated['webhook']['Id'];
                        $tenant->save();
                    }
                }
            }
        }

        $orchestratorConnection = OrchestratorConnection::find($orchestratorConnection->id);
        $extraInformation = [
            'verified' => $orchestratorConnection->verified,
            'verified_at_for_humans' => $orchestratorConnection->verified_at_for_humans,
        ];
        if ($orchestratorConnection->hosting_type === 'on_premise') {
            $extraInformation['tenantsVerificationStatuses'] = $orchestratorConnection->tenants->map->only([
                'id', 'verified', 'verified_at_for_humans'
            ])->toArray();
        }
        $this->flashVerificationStatus($result, $extraInformation);
        
        return redirect()->route('configuration.orchestrator-connections.edit', [
            'orchestrator_connection' => $orchestratorConnection->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrchestratorConnection  $orchestratorConnection
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrchestratorConnection $orchestratorConnection)
    {
        $name = $orchestratorConnection->name;
        $orchestratorConnection->delete();

        $result = $this->service->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
        if ($result['ok']) {
            foreach($orchestratorConnection->tenants as $tenant) {
                $webhookRemoved = $this->service->removeWebhook($orchestratorConnection, $tenant);
                if (!$webhookRemoved['ok']) {
                    // !important: notifier l'utilisateur
                }
            }
        }

        sleep(1);

        session()->flash('flash.banner', __('UiPath Orchestrator connection named :name successfully removed!', [
            'name' => $name
        ]));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('configuration.orchestrator-connections.index');
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
        OrchestratorConnection::destroy($selected);
        sleep(1);

        session()->flash('flash.banner', __('UiPath Orchestrator connections successfully removed!'));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('configuration.orchestrator-connections.index');
    }

    private function flashVerificationStatus($result, $extraInformation)
    {
        if ($result['ok']) {
            session()->flash('message', [
                'verify' => __('Connection verified successfully.'),
                'extraInformation' => $extraInformation,
            ]);
        } else {
            session()->flash('message', [
                'verify' => $result['error'] ? __('Connection verification failed: :error.', [
                    'error' => $result['error']
                ]) : __('Connection verification failed.'),
                'extraInformation' => $extraInformation,
                'tenantsResults' => isset($result['tenants_results']) ? $result['tenants_results'] : null,
            ]);
        }
    }

    /**
     * Verify the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrchestratorConnection  $orchestratorConnection
     * @return \Illuminate\Http\Response
     */
    public function verify(HttpRequest $request, $orchestratorConnection = null)
    {
        if ($orchestratorConnection !== null) {
            $orchestratorConnection = OrchestratorConnection::findOrFail($orchestratorConnection);
        }

        $result = false;
        $extraInformation = null;

        if ($orchestratorConnection) {
            // edit mode
            $result = $this->service->checkCredentialsWithOrchestratorConnection($orchestratorConnection);
            $orchestratorConnection = OrchestratorConnection::find($orchestratorConnection->id);

            $extraInformation = [
                'verified' => $orchestratorConnection->verified,
                'verified_at_for_humans' => $orchestratorConnection->verified_at_for_humans,
            ];
            if ($orchestratorConnection->hosting_type === 'on_premise') {
                $extraInformation['tenantsVerificationStatuses'] = $orchestratorConnection->tenants->map->only([
                    'id', 'verified', 'verified_at_for_humans'
                ])->toArray();
            }
        } else {
            // create mode
            $url = null;
            $hostingType = $request->hosting_type;
            if ($hostingType === 'on_premise') {
                $url = $request->url;
            }

            $endpoint = getUiPathOrchestratorEndpoint($hostingType, $url);
            if ($hostingType === 'cloud') {
                $clientID = $request->client_id;
                $clientSecret = $request->client_secret;
                $result = $this->service->checkCredentials($endpoint, $clientID, $clientSecret);
            } elseif ($hostingType === 'on_premise') {
                $result = $this->service->checkTenantsCredentials($endpoint, $request->tenants);
            }

            $extraInformation = [
                'verified' => $result['ok'],
                'verified_at_for_humans' => $result['date'] ? $result['date']->diffForHumans() : null,
            ];
            if ($hostingType === 'on_premise') {
                $extraInformation['tenantsVerificationStatuses'] = array_map(function ($result) {
                    return [
                        'id' => $result['id'],
                        'verified' => $result['ok'],
                        'verified_at_for_humans' => $result['date'] ? $result['date']->diffForHumans() : null,
                    ];
                }, $result['tenants_results']);
            }
        }

        $this->flashVerificationStatus($result, $extraInformation);

        if ($orchestratorConnection) {
            // edit mode
            return redirect()->route('configuration.orchestrator-connections.edit', [
                'orchestrator_connection' => $orchestratorConnection->id,
            ]);
        }
        return back(303);
    }

    /**
     * Handle UiPath Orchestrator webhook request.
     *
     * @param  $uuid
     * @return void
     */
    public function tenantsWebhookHandler($uuid)
    {
        $request = request();

        Log::info("Handling orchestrator connection tenant webhook for uuid $uuid");
        $orchestratorConnectionTenant = OrchestratorConnectionTenant::where('uuid', $uuid)->firstOrFail();
        
        $orchestratorConnection = $orchestratorConnectionTenant->orchestratorConnection;
        Log::info("Corresponding orchestrator connection tenant: id => $orchestratorConnectionTenant->id / name => $orchestratorConnectionTenant->name / orchestrator connection id => $orchestratorConnection->id / orchestrator connection name => $orchestratorConnection->name");
        
        if ($request->hasHeader('X-UiPath-Signature')) {
            $signature = $request->header('X-UiPath-Signature');
            Log::debug("signature in header: $signature");
            
            $secret = Crypt::decryptString($orchestratorConnectionTenant->webhook_secret);

            $payload = $request->getContent();
            Log::debug("request content: $payload");
            
            $hexHash = hash_hmac('sha256', $payload, utf8_encode($secret));
            $computedSignature = base64_encode(hex2bin($hexHash));
            Log::debug("computed signature: $computedSignature");

            $result = hash_equals($signature, $computedSignature);
            if ($result) {
                InterpretOrchestratorConnectionTenantWebhookEvent::dispatch($orchestratorConnectionTenant, $payload);
            } else {
                Log::error('invalid signature');
            }
        } else {
            Log::debug('no signature');
        }

        Log::info("Handled orchestrator connection tenant webhook for uuid $uuid");
    }
}
