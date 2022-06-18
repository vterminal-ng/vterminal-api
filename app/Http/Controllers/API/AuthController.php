<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\CheckCurrentAndNewPassword;
use App\Rules\CheckCurrentPassword;
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
            'role' => ['string', 'in:customer,merchant'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $params = [
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ];

        if ($request->has('role')) {
            $params['role'] = $request->role;
        }
        // dd($request->all());
        // create the user
        $user = User::create($params);

        //create token for user
        $token = $user->createToken("access Token")->plainTextToken;

        // return the token
        return $this->successResponse(
            "Registeration Successful",
            [
                "user" => new UserResource($user),
                "token" => $token,
            ],
            Response::HTTP_CREATED
        );
    }

    function login(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:15'],
            'password' => ['required'],
        ]);

        // find user with email
        $user = User::where('phone_number', $request->phoneNumber)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
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

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new CheckCurrentPassword()],
            'new_password' => ['required', 'min:6', new CheckCurrentAndNewPassword(), 'confirmed'],
        ]);

        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->newPassword)
        ]);

        $user->tokens()->delete();

        $token = $user->createToken("default")->plainTextToken;

        return $this->successResponse(
            "Password Updated Successfuly",
            [
                "token" => $token
            ]
        );
    }
}
