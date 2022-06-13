<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantDetailResource extends JsonResource
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
            "user_id" => $this->User->id,
            "name" => $this->business_name,
            "state" => $this->business_state,
            "address" => $this->business_adress,
            "verified_date" => $this->business_verified,
            "has_physical_location" => $this->has_physical_location
        ];
    }
}
