<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use Illuminate\Http\Response;

class UserDetailController extends Controller
{
    use ApiResponder;

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'unique:user_details'],
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'other_names' => ['string', 'min:3'],
            'date_of_birth' => ['required'],
            'gender' => ['required', 'in:male,female'],
            'referral_code' => ['string'],
            'referrer' => ['string']
        ]);

        User::findOrFail($request->user_id);

        // make sure that the user_id provided in the request belongs to the currently authenticated user 
        $this->authorize('create', $request->user_id);

        $userDetails = UserDetail::create([
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_names' => $request->other_names,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'referral_code' => $request->referral_code,
            'referrer' => $request->referrer,
        ]);

        return $this->successResponse(
            "User Details Added Successfully",
            [
                "userDetails" => new UserDetailResource($userDetails)
            ],
            Response::HTTP_CREATED
        );
    }

    public function read()
    {
        // $userId = auth()->id();

        // $userDetails = UserDetail::where('user_id', '=', $userId)->first();

        // if (!$userDetails) {
        //     return $this->failureResponse(
        //         "User Not Found",
        //         Response::HTTP_NOT_FOUND
        //     );
        // }

        $user = auth()->user();
        if (!$user->userDetail) {
            return $this->failureResponse(
                "No User Details",
                Response::HTTP_NOT_FOUND
            );
        }
        return $this->successResponse(
            "Details Found",
            [
                "userDetails" => new UserDetailResource($user->userDetail)
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
            ->userDetail
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
            [
                'userDetail' => new UserDetailResource($userDetail)
            ]
        );
    }
}
