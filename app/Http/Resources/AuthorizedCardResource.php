<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorizedCardResource extends JsonResource
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
            "authorizationCode" => $this->authorization_code,
            "cardType" => $this->card_type,
            "last4" => $this->last4,
            "expMonth" => $this->exp_month,
            "expYear" => $this->exp_year,
            "bin" => $this->bin,
            "bank" => $this->bank,
            "signature" => $this->signature,
            "accountName" => $this->account_name,
            "reference" => $this->reference,
            "createDates" => [
                'creadtedAtHuman' => $this->created_at->diffForHumans(),
                'creadtedAt' => $this->created_at,
            ]
        ];
    }
}
