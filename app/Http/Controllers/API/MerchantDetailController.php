<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDetailResource;
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
            'user_id' => ['required', 'integer', 'unique:merchant_details'],
            'business_name' => ['required', 'string', 'min:3', 'unique:merchant_details,business_name'],
            'business_state' => ['required', 'alpha', 'min:1'],
            'business_address' => ['required', 'string', 'min:5'],
            'has_physical_location' => ['required']
        ]);

        User::findOrFail($request->user_id);
        
        // make sure that the user_id provided in the request belongs to the currently authenticated user 
        $this->authorize('create', $request->user_id);

        $merchantDetails = MerchantDetail::create([
            'user_id' => $request->user_id,
            'business_name' => $request->business_name,
            'business_state' => $request->business_state,
            'business_address' => $request->business_address,
            'has_physical_location' => $request->has_physical_location
        ]);

        return $this->successResponse(
            "Merchant Successfully Created",
            [
                "merchantDetails" => new MerchantDetailResource($merchantDetails)
            ],
            Response::HTTP_CREATED
        );
    }

    public function read() {
        // $userId = auth()->id();

        // $merchant_details = MerchantDetail::where('user_id','=', $userId)->first();

        $user = auth()->user();
        if(!$user->MerchantDetail) {
            return $this->failureResponse(
                "No Merchant Details",
                Response::HTTP_NOT_FOUND
            );
        }
        return $this->successResponse(
            "Merchant Found",
            [
                "merchant_details" => new MerchantDetailResource($user->MerchantDetail)
            ],
            Response::HTTP_FOUND
        );

    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => ['string', 'min:3', 'unique:merchant_details,business_name'],
            'business_state' => ['alpha', 'min:1'],
            'business_address' => ['string', 'min:5'],
            'has_physical_location' => []
        ]);

        // get authenticated user instance
        $user = auth()->user();
        // dd($user);

        // using the relationship function between User and userDetail model to update the user details
        //  request->only() takes the an array of values we want to pick from the resquest
        $merchantDetail = $user
            ->MerchantDetail
            ->fill($request->only(
                [
                    'business_name',
                    'business_state',
                    'business_address',
                    'has_physical_location'
                ]
            ));

        if ($merchantDetail->isClean()) return $this->failureResponse('At least one value must change', Response::HTTP_NOT_ACCEPTABLE);

        $merchantDetail->save();

        return $this->successResponse(
            "Merchant Details Updated",
            [
                'merchantDetail' => new MerchantDetailResource($merchantDetail)
            ],
            Response::HTTP_ACCEPTED
        );
    }
}
