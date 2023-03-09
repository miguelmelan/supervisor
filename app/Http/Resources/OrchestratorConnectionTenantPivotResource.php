<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrchestratorConnectionTenantPivotResource extends JsonResource
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
            'built_in_alerts' => $this->built_in_alerts,
            'processes_supervision' => $this->processes_supervision,
            'machines_supervision' => $this->machines_supervision,
            'queues_supervision' => $this->queues_supervision,
            'kibana_built_in_alerts' => $this->kibana_built_in_alerts,
        ];
    }
}
