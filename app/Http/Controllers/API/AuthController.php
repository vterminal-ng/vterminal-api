<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponder;

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

        //TODO: Create OTP
        //TODO: Send OTP to user phone

        // return the token
        return $this->successResponse(
            "Registeration Successful",
            [
                "user" => new $user,
                "token" => $token,
            ],
            Response::HTTP_CREATED
        );
    }
}
