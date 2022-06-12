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
    
    public function create (Request $request) {
        $request->validate([
            'firstName' => ['required', 'string', 'min:3'],
            'lastName' => ['required', 'string', 'min:3'],
            'otherNames' => ['string', 'min:3'],
            'dateOfBirth' => ['required'],
            'gender' => ['required', 'in:male,female'],
            'referralCode' => ['string'],
            'referrer' => ['string']
        ]);

        $user_details = UserDetail::create([
            'user_id' => auth('sanctum')->user()->id,
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

    public function read (Request $request, User $user) {
        
    }

    public function update () {}
}
