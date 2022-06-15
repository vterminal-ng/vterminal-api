<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponder;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Str;

class ResetPasswordController extends Controller
{
    use ApiResponder;

    /**
     * sendResetLinkEmail
     * 
     * Send a reset link with a token to the existing user email
     *
     * @param  mixed $request
     * @return void
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            return $this->failureResponse(
                ['email' => [trans($status)]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->successResponse('Reset link has been sent to your email');
    }

    /**
     * reset 
     * Change the user password
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'confirmed', RulesPassword::defaults()]
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            return $this->failureResponse(
                __($status),
                Response::HTTP_FORBIDDEN
            );
        }

        return $this->successResponse('Password Reset Successfully');
    }
}
