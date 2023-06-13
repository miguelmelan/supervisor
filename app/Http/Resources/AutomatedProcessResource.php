<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutomatedProcessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'health' => $this->health,
            'orchestratorConnections' => OrchestratorConnectionResource::collection($this->orchestratorConnections),
            'orchestratorConnectionTenants' => OrchestratorConnectionTenantResource::collection($this->orchestratorConnectionTenants),
            'properties' => PropertyResource::collection($this->properties),
            'tags' => TagResource::collection($this->tags),
            'releases' => OrchestratorConnectionTenantReleaseResource::collection($this->releases),
            'machines' => OrchestratorConnectionTenantMachineResource::collection($this->machines),
            'queues' => OrchestratorConnectionTenantQueueResource::collection($this->queues),
            //'triggers' => AIBasedAlertTriggerResource::collection($this->triggers),
            'alerts_count' => $this->whenCounted('alerts'),
        ];
    }
}
