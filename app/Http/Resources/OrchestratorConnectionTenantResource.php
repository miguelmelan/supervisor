<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrchestratorConnectionTenantResource extends JsonResource
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
            'name' => $this->name,
            'client_id' => $this->client_id,
            'client_secret' => '',
            'verified' => $this->verified,
            'verified_at_for_humans' => $this->verified_at_for_humans,
            'pivot' => new OrchestratorConnectionTenantPivotResource($this->pivot),
            'alerts_count' => $this->whenCounted('alerts'),
            'orchestrator_connection' => new OrchestratorConnectionResource($this->whenLoaded('orchestratorConnection')),
        ];
    }
}
