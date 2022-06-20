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
            "buisnessName" => $this->business_name,
            "state" => $this->business_state,
            "address" => $this->business_address,
            "verifiedDate" => $this->business_verified,
            "hasPhysicalLocation" => $this->has_physical_location
        ];
    }
}
