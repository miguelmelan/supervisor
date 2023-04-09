<?php

namespace App\Http\Resources;

use App\Models\OrchestratorConnectionTenant;
use Illuminate\Http\Resources\Json\JsonResource;

class OrchestratorConnectionTenantAlertResource extends JsonResource
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
            'id_padded' => str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'tenant_id' => $this->tenant_id,
            'automated_process_id' => $this->automated_process_id,
            'external_id' => $this->external_id,
            'notification_name' => $this->notification_name,
            'data' => $this->data,
            'component' => $this->component,
            'severity' => $this->severity,
            'creation_time' => $this->creation_time,
            'deep_link_relative_url' => $this->deep_link_relative_url,
            'read_at' => $this->read_at,
            'resolution_time_in_seconds' => $this->resolution_time_in_seconds,
            'locked_at' => $this->locked_at,
            'tenant' => new OrchestratorConnectionTenantResource(OrchestratorConnectionTenant::find($this->tenant_id)->load('orchestratorConnection')),
        ];
    }
}
