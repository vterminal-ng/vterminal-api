<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

trait OtpCheck
{
    /**
     * isOtpExpired
     *
     * @param  mixed $createTime
     * @return bool
     */
    public function isOtpExpired($createTime)
    {
        $now = Carbon::now()->timestamp;

        $timeDifference = $now - strtotime($createTime);

        $minutes = round($timeDifference / 60);

        // return true, if $minutes is greater than the otp expiration duration in minutes, default is 60 minutes.
        return $minutes > config('auth.passwords.users.expire');
    }

    public function checkOtpValidity(Request $request, $otpRecord)
    {
        if (!Hash::check($request->otp, $otpRecord->otp) || $this->isOtpExpired($otpRecord->created_at)) {
            return $this->failureResponse("Incorrect or expired otp", Response::HTTP_UNAUTHORIZED);
        }
    }

    public function checkIfUserCanVerifyThisOtp(User $user)
    {
        if (!(auth()->id() === $user->id)) {
            throw new AuthorizationException();
        }
    }
}
