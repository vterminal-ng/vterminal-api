<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
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
            "amount" => $this->amount,
            "type" => $this->type,
            "previousBalance" => $this->meta['previous_balance'] ?? 'N/A',
            "meta" => $this->meta,
            "reference" => $this->uuid,
            "createDates" => [
                'creadtedAtHuman' => $this->created_at->diffForHumans(),
                'creadtedAt' => $this->created_at,
            ]
        ];
    }
}
