<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankDetailResource extends JsonResource
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
            "accountName" => $this->account_name,
            "accountNumber" => $this->account_number,
            "bankName" => $this->bank_name,
            "isVerified" => $this->is_verified,
            "user" => new UserResource($this->whenLoaded('user')),
        ];
    }
}
