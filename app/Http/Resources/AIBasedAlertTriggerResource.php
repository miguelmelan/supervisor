<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AIBasedAlertTriggerResource extends JsonResource
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
            'orchestratorConnections' => OrchestratorConnectionResource::collection($this->orchestratorConnections),
            'orchestratorConnectionTenants' => OrchestratorConnectionTenantResource::collection($this->orchestratorConnectionTenants),
            'tags' => TagResource::collection($this->tags),
            'releases' => OrchestratorConnectionTenantReleaseResource::collection($this->releases),
            'machines' => OrchestratorConnectionTenantMachineResource::collection($this->machines),
            'queues' => OrchestratorConnectionTenantQueueResource::collection($this->queues),
            'conditions' => $this->conditions,
            'recurrence' => $this->recurrence,
            'crons' => $this->crons,
        ];
    }
}
