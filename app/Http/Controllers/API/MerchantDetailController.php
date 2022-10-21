<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Jobs\AddressUpload;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MerchantDetail;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDetailResource;
use Image;


class MerchantDetailController extends Controller
{
    use ApiResponder;

    public function create(Request $request)
    {
        $request->validate([
            'business_name' => ['required', 'string', 'min:3', 'unique:merchant_details,business_name'],
            'business_state' => ['required', 'string', 'min:1'],
            'business_city' => ['required', 'string', 'min:1'],
            'business_address' => ['required', 'string', 'min:5'],
            'has_physical_location' => ['required']
        ]);

        $user = User::find(auth()->id());

        // make sure that the user_id provided in the request belongs to the currently authenticated user 
        //$this->authorize('create', $request->user_id);

        $merchantDetails = $user->merchantDetail()->create([
            'business_name' => $request->business_name,
            'business_state' => $request->business_state,
            'business_city' => $request->business_city,
            'business_address' => $request->business_address,
            'has_physical_location' => $request->has_physical_location
        ]);

        return $this->successResponse(
            "Merchant Successfully Created",
            new MerchantDetailResource($merchantDetails),
            Response::HTTP_CREATED
        );
    }

    public function read()
    {
        // $userId = auth()->id();

        // $merchant_details = MerchantDetail::where('user_id','=', $userId)->first();

        $user = auth()->user();
        if (!$user->MerchantDetail) {
            return $this->failureResponse(
                "No Merchant Details",
                Response::HTTP_NOT_FOUND
            );
        }
        return $this->successResponse(
            "Merchant Found",
            new MerchantDetailResource($user->MerchantDetail),
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
            new MerchantDetailResource($merchantDetail),
            Response::HTTP_ACCEPTED
        );
    }

    public function addressConfirmation(Request $request){
        //validate request body
        $request->validate([
            'address_confirmation' => ['mimes:png,jpeg,gif,bmp', 'max:2048','required'],
            

          
        ]);

        //get the image
        $image = $request->file('image');
        //$image_path = $image->getPathName();
 
        // get original file name and replace any spaces with _
        // example: ofiice card.png = timestamp()_office_card.pnp
        $filename = time()."_".preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
 
        // move image to temp location (tmp disk)
        $tmp = $image->storeAs('uploads/address', $filename, 'tmp');
 
 
        //create the upload
        $newDetail = MerchantDetail::create([
            'user_id'=>auth()->id(),
            'address_confirmation'=> $filename,
            'disk'=> config('site.upload_disk'),
           
            
        ]);

        //dispacth job to handle image manipulation
        $this->dispatch(new AddressUpload($newDetail));

        //return cuccess response

        return response()->json([
            'success'=> true,
            'message'=>'successfully uploaded a file',
            'data' => $newDetail
        ]);
    }
}
