<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrchestratorConnectionResource extends JsonResource
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
            'hosting_type' => $this->hosting_type,
            'environment_type' => $this->environment_type,
            'client_id' => $this->client_id,
            'client_secret' => '',
            'url' => $this->url,
            'organization_name' => $this->organization_name,
            'tenants' => OrchestratorConnectionTenantResource::collection($this->tenants),
            'elasticsearch_enabled' => $this->elasticsearch_enabled,
            'elasticsearch_index_configuration' => $this->elasticsearch_index_configuration,
            'elasticsearch_url' => $this->elasticsearch_url,
            'elasticsearch_anonymous_authentication' => $this->elasticsearch_anonymous_authentication,
            'elasticsearch_username' => $this->elasticsearch_username,
            'elasticsearch_password' => '',
            'kibana_enabled' => $this->kibana_enabled,
            'kibana_url' => $this->kibana_url,
            'verified' => $this->verified,
            'verified_at_for_humans' => $this->verified_at_for_humans,
            'properties' => PropertyResource::collection($this->properties),
            'tags' => TagResource::collection($this->tags),
            'pivot' => new OrchestratorConnectionPivotResource($this->pivot),
            'alerts_count' => $this->whenCounted('alerts'),
        ];
    }
}
