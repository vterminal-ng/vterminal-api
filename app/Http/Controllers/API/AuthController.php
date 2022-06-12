<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TermiiService;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Carbon\Carbon;
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

    function login(Request $request) {
        $request->validate([
            'phoneNumber' => ['required', 'string', 'max:15'],
            'password' => ['required'],
        ]);

        // find user with email
        $user = User::where('phone_number', $request->phoneNumber)->first();

        if(!$user || !Hash::check($request->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect login credentials'
            ]);
        }

        // delete any existing token for the user
        $user->tokens()->delete();

        // create a new token for the user
        $token = $user->createToken("login")->plainTextToken;

        return $this->successResponse(
            "Login Successful",
            [
                "token" => $token,
            ],
            Response::HTTP_OK
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

        $user->phone_number_verified_at = Carbon::now();

        return $this->successResponse("Verified", $verificationResponse);
    }
}
