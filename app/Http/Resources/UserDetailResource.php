<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            "firstName" => $this->first_name,
            "lastName" => $this->last_name,
            "fullName" => $this->fullname,
            "initials" => strtoupper($this->initials),
            "otherNames" => $this->other_names,
            "dateOfBirth" => $this->date_of_birth,
            "gender" => $this->gender,
            "referralCode" => $this->referral_code,
            "referrer" => $this->referrer,
            "profilePicture" => Storage::disk('public')->url($this->profile_picture),
            "user" => new UserResource($this->whenLoaded('user')),

        ];
    }
}
