<?php

namespace App\Http\Resources;

use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantAlert;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
        $locale = app()->getLocale();
        $hasCommentsRelation = $this->resource instanceof(OrchestratorConnectionTenantAlert::class);

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
            'creation_time' => Carbon::parse($this->creation_time)->format(config("languages.{$locale}.dateFormats.php"). ' ' . config("languages.{$locale}.timeFormats.php")),
            'creation_time_for_humans' => Carbon::parse($this->creation_time)->diffForHumans(Carbon::now()),
            'deep_link_relative_url' => $this->deep_link_relative_url,
            'read_at' => $this->read_at ? Carbon::parse($this->read_at)->format(config("languages.{$locale}.dateFormats.php"). ' ' . config("languages.{$locale}.timeFormats.php")) : null,
            'read_by' => $this->read_by ? new UserResource(User::find($this->read_by)) : null,
            'resolution_time_in_seconds' => $this->resolution_time_in_seconds,
            'locked_at' => $this->locked_at ? Carbon::parse($this->locked_at)->format(config("languages.{$locale}.dateFormats.php"). ' ' . config("languages.{$locale}.timeFormats.php")) : null,
            'locked_by' => $this->locked_by ? new UserResource(User::find($this->locked_by)) : null,
            'resolution_details' => $this->resolution_details,
            'false_positive' => $this->false_positive,
            'tenant' => new OrchestratorConnectionTenantResource(OrchestratorConnectionTenant::find($this->tenant_id)->load('orchestratorConnection')),
            'comments' => $hasCommentsRelation ? $this->whenLoaded('comments', CommentResource::collection($this->comments()->approved()->orderBy('id', 'desc')->get())) : null,
        ];
    }
}
