<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            "id" => $this->id,
            "user_id" => $this->user_id,
            "user" => new UserResource($this->User),
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "other_names" => $this->other_names,
            "date_of_birth" => $this->date_of_birth,
            "gender" => $this->gender,
            "referral_code" => $this->referral_code,
            "referrer" => $this->referrer
        ];
    }
}
