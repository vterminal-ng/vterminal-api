<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TermiiService;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponder, ConsumeExternalService;

    protected $termiiService;

    public function __construct(TermiiService $termiiService)
    {
        $this->termiiService = $termiiService;
    }

    public function register(Request $request)
    {

        // validate the request
        $request->validate([
            'phone_number' => ['required', 'string', 'max:15', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // dd($request->all());
        // create the user
        $user = User::create([
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);


        //create token for user
        $token = $user->createToken("access Token")->plainTextToken;

        // send otp
        $otpData = $this->termiiService->sendOtp($user);

        // return the token
        return $this->successResponse(
            "Registeration Successful",
            [
                "user" => $user,
                "otp" => $otpData,
                "token" => $token,
            ],
            Response::HTTP_CREATED
        );
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'pin_code' => ['required', 'string', 'size:6'],
        ]);

        $user = auth('sanctum')->user();
        // dd($user);

        $verificationResponse = $this->termiiService->verifyOtp($user, $request->pin_code);

        return $this->successResponse("Verified", $verificationResponse);
    }
}
