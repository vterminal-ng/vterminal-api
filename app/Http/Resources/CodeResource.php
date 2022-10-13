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
            'id' => $this->id,
            'code' => $this->code,
            'transactionType' => $this->transaction_type,
            'paymentMethod' => $this->payment_method,
            'status' => $this->status,
            'subtotalAmount' => $this->subtotal_amount,
            'totalAmount' => $this->total_amount,
            'chargeAmount' => $this->charge_amount,
            'chargeFrom' => $this->charge_from,
            'bankName' => $this->bank_name,
            'bank_code' => $this->bank_code,
            'accountName' => $this->account_name,
            'accountNumber' => $this->account_number,
            'paystackTransferRecipientCode' => $this->paystack_transfer_recipient_code,
            'reference' => $this->reference,
            "createDates" => [
                'creadtedAtHuman' => $this->created_at->diffForHumans(),
                'creadtedAt' => $this->created_at,
            ],
            'customer' => new UserResource($this->whenLoaded('customer')),
            'merchant' => new UserResource($this->whenLoaded('merchant')),
        ];
    }
}
