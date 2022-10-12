<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\PaystackService;
use App\Services\VerifyMeService;
use Illuminate\Http\Request;
use App\Traits\ApiResponder;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Image;

class UserDetailController extends Controller
{
    use ApiResponder;

    protected $paystackService;
    protected $verifyMeService;

    public function __construct(PaystackService $paystackService, VerifyMeService $verifyMeService)
    {
        $this->paystackService = $paystackService;
        $this->verifyMeService = $verifyMeService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'other_names' => ['nullable', 'string'],
            'date_of_birth' => ['required', 'date_format:Y-m-d'],
            'gender' => ['required', 'in:male,female'],
            //'referral_code' => ['string'],
            'referrer' => ['nullable', 'string'],
        ]);

        $now = strtotime(Carbon::now()->format('Y-m-d'));
        $birthDate = strtotime($request->date_of_birth);
        $ageDifference = ($now - $birthDate)/365/60/60/24;
        
        if($ageDifference < 18) {
            return $this->failureResponse("Sorry, persons below the age of 18 years are not allowed to use our service.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        if ($request->referrer != '' || !is_null($request->referrer)) {
            //check if the referrer code exists
            $userDetail = UserDetail::where('referral_code', $request->referrer)->first();
            if (!$userDetail) {
                return $this->failureResponse(['referrer' => ['Invalid referral code']], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        // get authenticated user instance
        $user = User::find(auth()->id());

        if ($user->userDetail) {
            return $this->failureResponse("This user already have details, Use update route instead", Response::HTTP_BAD_REQUEST);
        }

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
            new UserDetailResource($userDetails),
            Response::HTTP_CREATED
        );
    }

    public function read()
    {
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
        $user = User::find(auth()->id());
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
                    "image_path" => 'storage/' . $avatar
                ],
                Response::HTTP_OK
            );
        } else {
            return $this->failureResponse("Please select a valid image file", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function verifyBvn(Request $request)
    {
        $request->validate([
            'bvn' => ['required'],
            'account_no' => ['required'],
            'bank_code' => ['required']
        ]);

        $user = auth()->user();

        if (!$user->userDetail) {
            return $this->failureResponse("Please complete your profile before you continue", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bvn = $request->bvn;

        $accountInfo = $this->verifyMeService->getAccountDetails($request->bank_code, $request->account_no);

        // Compare nuban bvn and verifyMe bvn
        // We can condition both name and bvn check together but i separated them to 
        // know where the verification failure emanates from

        if ($accountInfo->data->bvn !== $bvn) {
            return $this->failureResponse(
                "BVN does not match",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $dbLastname = strtolower($user->userDetail->last_name);
        $verifyLastname = strtolower($accountInfo->data->lastname);

        //dd($user->userDetail->last_name . " " . strtolower($accountInfo->data->lastname) );
        if ($dbLastname !== $verifyLastname) {
            return $this->failureResponse(
                "Last name does not match",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // create paystack customer code
        $paystackCustomer = $this->paystackService->createCustomer($user->email, $user->userDetail->first_name, $dbLastname, $user->phone, NULL);
        // dd($paystackCustomer);
        $customerCode = $paystackCustomer->data->customer_code;
        // // create dedicated account TODO: REGISTER BUISNESS ACCOUNT
        $virtual = $this->paystackService->createDedicatedVirtualAccount($customerCode);
        dd($virtual);
    }
}
