<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use Illuminate\Http\Response;

class UserDetailController extends Controller
{
    use ApiResponder;
    
    public function create (Request $request, User $user) {
        $request->validate([
            'firstName' => ['required', 'string', 'min:3'],
            'lastName' => ['required', 'string', 'min:3'],
            'otherNames' => ['string', 'min:3'],
            'dateOfBirth' => ['required'],
            'gender' => ['required', 'in:male,female'],
            'referralCode' => ['string'],
            'referrer' => ['string']
        ]);

        $userId = auth()->id();
        
        $user_details = UserDetail::create([
            'user_id' => $userId,
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'other_names' => $request->otherNames,
            'date_of_birth' => $request->dateOfBirth,
            'gender' => $request->gender,
            'referral_code' => $request->referralCode,
            'referrer' => $request->referrer,
        ]);

        return $this->successResponse(
            "User Details Added Successfully",
            [
                "user_details" => $user_details
            ],
            Response::HTTP_CREATED
        );
    }

    public function read (User $user) {
        $userId = auth()->id();

        $userDetails = UserDetail::where('user_id','=', $userId)->first();

        if(!$userDetails) {
            return $this->failureResponse(
                "User Not Found",
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->successResponse(
            "Details Found",
            [
                "user_details" => $userDetails
            ],
            Response::HTTP_FOUND
        );

    }

    public function update (Request $request) {
        $request->validate([
            'firstName' => ['required', 'string', 'min:3'],
            'lastName' => ['required', 'string', 'min:3'],
            'otherNames' => ['string', 'min:3'],
            'dateOfBirth' => ['required'],
            'gender' => ['required', 'in:male,female']
        ]);

        $userId = auth()->id();

        $details = UserDetail::where('user_id','=', $userId)->get();

        if(!$details) {
            return $this->failureResponse(
                "User Details Not Found",
                Response::HTTP_NOT_FOUND
            );
        }

        $details->update([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'other_names' => $request->otherNames,
            'date_of_birth' => $request->dateOfBirth,
            'gender' => $request->gender
        ]);
        
        return $this->successResponse(
            "User Details Updated",
            Response::HTTP_OK
        );
    }
}
