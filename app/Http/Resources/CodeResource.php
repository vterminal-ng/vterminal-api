<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CodeResource extends JsonResource
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
            'customer' => $this->customer,
            'merchant' => $this->merchant,
            'code' => $this->code,
            'transactionType' => $this->transaction_type,
            'status' => $this->status,
            'subtotalAmount' => $this->subtotal_amount,
            'totalAmount' => $this->total_amount,
            'chargeAmount' => $this->charge_amount,
            'chargeFrom' => $this->charge_from,
            'reference' => $this->reference,
        ];
    }
}
