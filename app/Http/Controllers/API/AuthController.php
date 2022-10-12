<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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

        // find user with phone number
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->failureResponse('Incorrect login credentials', Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->is_active) {
            return $this->failureResponse('User account is currently blocked', Response::HTTP_UNAUTHORIZED);
        }

        // delete any existing token for the user
        $user->tokens()->delete();

        // create a new token for the user
        $token = $user->createToken("login")->plainTextToken;

        // update the updated_at column
        $user->touch();

        return $this->successResponse(
            "Login Successful",
            [
                "token" => $token,
                "user" => new UserResource($user),
            ]
        );
    }

    function emailLogin(Request $request)
    {
        // logging in with email and password

        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // find user with email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->failureResponse('Incorrect login credentials', Response::HTTP_UNAUTHORIZED);
        }

        // delete any existing token for the user
        $user->tokens()->delete();

        // create a new token for the user
        $token = $user->createToken("login")->plainTextToken;

        // update the updated_at column
        $user->touch();

        return $this->successResponse(
            "Login Successful",
            [
                "user" => new UserResource($user),
                "token" => $token,
            ]
        );
    }

    function logout()
    {
        // delete token for the logged in user
        auth("sanctum")->user()->tokens()->delete();

        return $this->successResponse("Logout Successful");
    }
}
