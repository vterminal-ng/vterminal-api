<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendPasswordResetOtp;
use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\OtpCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Str;

class ResetPasswordController extends Controller
{
    use ApiResponder, OtpCheck;

    /**
     * sendResetOtpEmail
     * 
     * Send a reset OTP to the existing user email
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function sendResetOtpEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email']
        ]);

        // gen otp
        $otp = rand(100000, 999999);

        $count = $record = DB::table('password_reset_otps')->where('email', $request->email)->count();


        if ($count) {
            $record = DB::table('password_reset_otps')->where('email', $request->email)->first();

            $lastTime = $record->created_at;

            $timeDifference = Carbon::now()->timestamp - strtotime($lastTime);

            if ($timeDifference < config('auth.passwords.users.throttle')) {
                return $this->failureResponse(['email' => ['Please wait before retrying.']], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::table('password_reset_otps')->where('email', $request->email)->delete();
        }

        $record = DB::table('password_reset_otps')->insert([
            'email' => $request->email,
            'otp' => Hash::make($otp),
            'created_at' => Carbon::now()
        ]);

        if (!$record) {
            return $this->failureResponse("Something went wrong. Contact administrator.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Mail::to($request->email)->send(new SendPasswordResetOtp($otp));

        return $this->successResponse('Reset OTP has been sent to your email');
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
            'otp' => 'required',
            'email' => ['required', 'email', 'exists:password_reset_otps,email'],
            'password' => ['required', 'min:8', 'confirmed', RulesPassword::defaults()]
        ]);

        // get record by email
        // check otp hash
        // check if expired
        $record = DB::table('password_reset_otps')->where('email', $request->email)->first();

        $this->checkOtpValidity($request, $record);

        User::where('email', $request->email)->first()->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return $this->successResponse('Password Reset Successfully');
    }
}
