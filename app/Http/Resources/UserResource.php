<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = [
            "id" => $this->id,
            "phoneNumber" => $this->phone_number,
            "emailAddress" => $this->email,
            "role" => $this->role,
            "hasVerifiedPhone" => $this->hasVerifiedPhone(),
            "hasVerifiedEmail" => $this->hasVerifiedEmail(),
            "hasBvnVerifed" => $this->hasVerifiedBvn(),
            "isProfileVerifed" => $this->isProfileVerified(),
            "hasSetPin" => $this->hasSetPin(),
            "userDetails" => new UserDetailResource($this->userDetail),
            "bankDetails" => new BankDetailResource($this->bankDetail),
            "walletBalance" => $this->balance,
            "walletTransactions" => WalletTransactionResource::collection($this->transactions)->sortByDesc('created_at')->values()->all(),
            "card" => new AuthorizedCardResource($this->authorizedCard),
        ];

        // if ($this->isMerchant()) {
        $user['merchantDetails'] = new MerchantDetailResource($this->merchantDetail);
        // }

        $user['createDates'] = [
            'createdAtHuman' => $this->created_at->diffForHumans(),
            'createdAt' => $this->created_at,
        ];

        $user['updateDates'] = [
            'updatedAtHuman' => $this->updated_at->diffForHumans(),
            'updatedAt' => $this->updated_at,
        ];

        return $user;
    }
}
