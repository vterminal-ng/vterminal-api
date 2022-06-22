<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CheckCurrentAndNewPassword;
use App\Rules\CheckCurrentPassword;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponder;

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // dd(auth()->user());

        $user = User::findOrFail(auth()->id());

        $user->fill($request->only('email'));

        if ($user->isClean()) return $this->failureResponse('At least one value must change', Response::HTTP_NOT_ACCEPTABLE);

        // mark user new email as unverified
        $user->fill([
            'email_verified_at' => null,
        ]);

        $user->save();

        return $this->successResponse("Email updated, Please verify new email");
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