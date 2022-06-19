<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendVerificationOtp;
use App\Models\User;
use App\Services\TermiiService;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class OtpController extends Controller
{
    use ApiResponder;

    protected $termiiService;

    public function __construct(TermiiService $termiiService)
    {
        $this->termiiService = $termiiService;
    }

    public function sendSmsOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:15', 'exists:users,phone_number'],
        ]);


        $user = User::where('phone_number', '=', $request->phone_number)->first();

        // check if phone number is already verified 
        if ($user->hasVerifiedPhone()) {
            return $this->failureResponse('Phone number is already verified', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // generate random 6 digit value
        $otp = rand(100000, 999999);

        // save OTP to databse
        $user->forceFill(['phone_otp' => $otp])->save();

        // The sms message
        $message = "Your Vterminal OTP is $otp. If you did not request a OTP, no further action is required.";

        // Send SMS
        $response = $this->termiiService->sendSms($request->phone_number, $message);

        return $this->successResponse($response->message);
    }

    public function verifySmsOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:15', 'exists:users,phone_number'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user  = User::where([['phone_number', '=', $request->phone_number], ['phone_otp', '=', $request->otp]])->first();

        if (!$user) {
            return $this->failureResponse('Invalid OTP', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        User::where('phone_number', '=', $request->phone_number)->update(['phone_otp' => null]);

        $user->markPhoneAsVerified();

        return $this->successResponse("Phone number verified");
    }

    public function sendEmailOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', '=', $request->email)->first();

        // check if phone number is already verified 
        if ($user->hasVerifiedEmail()) {
            return $this->failureResponse('Email is already verified', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // generate random 6 digit value
        $otp = rand(100000, 999999);

        // save OTP to databse
        $user->forceFill(['email_otp' => $otp])->save();


        Mail::to($request->email)->send(new SendVerificationOtp($otp));

        return $this->successResponse("OTP sent to $request->email");
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user  = User::where([['email', '=', $request->email], ['email_otp', '=', $request->otp]])->first();

        if (!$user) {
            return $this->failureResponse('Invalid OTP', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->forceFill(['email_otp' => null])->save();

        $user->markEmailAsVerified();

        return $this->successResponse("Email verified");
    }
}
