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

    public function create(Request $request, User $user)
    {
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

    public function read(User $user)
    {
        $userId = auth()->id();

        $userDetails = UserDetail::where('user_id', '=', $userId)->first();

        if (!$userDetails) {
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

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => ['string', 'min:3'],
            'last_name' => ['string', 'min:3'],
            'other_names' => ['string', 'min:3'],
            'date_of_birth' => [],
            'gender' => ['in:male,female']
        ]);

        // $userId = auth()->id();

        // $details = UserDetail::where('user_id','=', $userId)->get();

        // if(!$details) {
        //     return $this->failureResponse(
        //         "User Details Not Found",
        //         Response::HTTP_NOT_FOUND
        //     );
        // }

        // $details->update([
        //     'first_name' => $request->firstName,
        //     'last_name' => $request->lastName,
        //     'other_names' => $request->otherNames,
        //     'date_of_birth' => $request->dateOfBirth,
        //     'gender' => $request->gender
        // ]);

        // get authenticated user instance
        $user = auth()->user();
        // dd($user);

        // using the relationship function between User and userDetail model to update the user details
        //  request->only() takes the an array of values we want to pick from the resquest
        $userDetail = $user
            ->UserDetail
            ->fill($request->only(
                [
                    'first_name',
                    'last_name',
                    'other_names',
                    'date_of_birth',
                    'gender',
                ]
            ));

        if ($userDetail->isClean()) return $this->failureResponse('At least one value must change', Response::HTTP_NOT_ACCEPTABLE);

        $userDetail->save();

        return $this->successResponse(
            "User Details Updated",
            $userDetail,
            Response::HTTP_OK
        );
    }
}
