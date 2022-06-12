<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MerchantDetail;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MerchantDetailController extends Controller
{
    use ApiResponder;

    public function create(Request $request) {
        $request->validate([
            'business_name' => ['required', 'string', 'min:3', 'unique:merchant_details,business_name'],
            'business_state' => ['required', 'alpha', 'min:1'],
            'business_address' => ['required', 'string', 'min:5'],
            'has_physical_location' => ['required']
        ]);

        $userId = auth()->id();

        MerchantDetail::create([
            'user_id' => $userId,
            'business_name' => $request->business_name,
            'business_state' => $request->business_state,
            'business_address' => $request->business_address,
            'has_physical_location' => $request->has_physical_location
        ]);

        return $this->successResponse(
            "Merchant Successfully Created",
            Response::HTTP_CREATED
        );
    }

    public function read() {
        $userId = auth()->id();

        $merchant_details = MerchantDetail::where('user_id','=', $userId)->first();
        return $this->successResponse(
            "Merchant Found",
            [
                "merchant_details" => $merchant_details
            ],
            Response::HTTP_FOUND
        );

    }
}
