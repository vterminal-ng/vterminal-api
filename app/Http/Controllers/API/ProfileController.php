<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\EmailUpdated;
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
        $user->forceFill([
            'email_verified_at' => null,
        ]);

        $user->save();

        $user->notify(new EmailUpdated($user));
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

    public function generateApiKey(Request $request)
    {
        // Generate an API KEY
        $api_key = "pk_" . floor(microtime(true) * 1000);

        $user = auth()->user();

        $user->api_key = $api_key;
        $user->save();

        return $this->successResponse(
            "API KEY Generated",
            [
                "api_key" => $api_key
            ]
        );
    }

    public function reGenerateApiKey(Request $request)
    {
        $user = auth()->user();

        $api_key = "pk_" . floor(microtime(true) * 1000);

        $user->api_key = $api_key;
        $user->save();

        return $this->successResponse(
            "API KEY Updated",
            [
                "api_key" => $api_key
            ]
        );
    }

    public function getApiKey()
    {
        $api_key = auth()->user()->api_key;

        if(!$api_key) return $this->failueResponse("User hasn't generated an API KEY", Response::HTTP_NO_CONTENT);

        return $this->successResponse(
            "API KEY",
            [
                "api_key" => $api_key
            ]
        );
    }
}
