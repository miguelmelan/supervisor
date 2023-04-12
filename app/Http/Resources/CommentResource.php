<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'comment' => $this->comment,
            'created_at' => Carbon::create($this->created_at)->format('Y-m-d H:i:s'),
            'created_at_for_humans' => Carbon::create($this->created_at)->diffForHumans(),
            'updated_at' => $this->updated_at,
            'user' => new UserResource(User::find($this->user_id)),
        ];
    }
}
