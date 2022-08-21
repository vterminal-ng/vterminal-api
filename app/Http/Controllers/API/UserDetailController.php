<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Image;

class UserDetailController extends Controller
{
    use ApiResponder;

    public function create(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'other_names' => ['string', 'min:3'],
            'date_of_birth' => ['required'],
            'gender' => ['required', 'in:male,female'],
            //'referral_code' => ['string'],
            'referrer' => ['string']
        ]);

        // get authenticated user instance
        $user = auth()->user();

        $refCode = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);

        $userDetails = $user->userDetail()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_names' => $request->other_names,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'referral_code' => $refCode,
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

        // get authenticated user instance
        $user = auth()->user();

        // dd($user->userDetail);
        if (!$user->userDetail) {
            return $this->failureResponse(
                "User Details Not Found",
                Response::HTTP_NOT_FOUND
            );
        }

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

    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();
        if (!$user->userDetail) {
            return $this->failureResponse(
                "No user details. Please update user details first.",
                Response::HTTP_NOT_FOUND
            );
        }

        $request->validate([
            'image' => ['mimes:png,jpg,jpeg', 'max:2048']
        ]);

        if ($request->hasFile('image')) {

            $destination = 'storage/' . $user->userDetail->profile_picture;

            if (File::exists($destination)) {
                File::delete($destination);
            }

            $avatar = $request->file('image')->store('avatars', 'public');

            $user->userDetail()->update([
                'profile_picture' => $avatar
            ]);

            return $this->successResponse(
                "Profile Picture Updated",
                [
                    "image_path" => 'storage/'.$avatar
                ],
                Response::HTTP_OK
            );
        } else {
            return $this->failureResponse("Please select a valid image file", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        

    }

}
