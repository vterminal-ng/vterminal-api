<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VirtualAccountResource extends JsonResource
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
            'bank_name' => $this->identity_type,
            'account_number' => $this->identity_number,
            'account_name' => $this->first_name,
            "createDates" => [
                'creadtedAtHuman' => $this->created_at->diffForHumans(),
                'creadtedAt' => $this->created_at,
            ],
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
