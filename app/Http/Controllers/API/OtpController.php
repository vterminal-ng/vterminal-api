<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendVerificationOtp;
use App\Models\User;
use App\Services\TermiiService;
use App\Traits\ApiResponder;
use App\Traits\OtpCheck;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    use ApiResponder, OtpCheck;

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

        $this->checkIfUserCanVerifyThisOtp($user);

        // check if phone number is already verified 
        if ($user->hasVerifiedPhone()) {
            return $this->failureResponse('Phone number is already verified', Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        // check if you are trying to resend otp
        $count = DB::table('mobile_verification_otps')->where('phone_number', $request->phone_number)->count();
        if ($count) {
            $record = DB::table('mobile_verification_otps')->where('phone_number', $request->phone_number)->first();

            $lastTime = $record->created_at;

            $timeDifference = Carbon::now()->timestamp - strtotime($lastTime);

            // wait one minute before retrying
            if ($timeDifference < 60) {
                return $this->failureResponse('Please wait before retrying.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // delete old otp
            DB::table('mobile_verification_otps')->where('phone_number', $request->phone_number)->delete();
        }

        // generate random 6 digit value
        $otp = rand(100000, 999999);

        try {
            // save new OTP to databse
            DB::table('mobile_verification_otps')->insert([
                'phone_number' => $request->phone_number,
                'otp' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            throw $e;
        }

        // The sms message
        $message = "Your Vterminal OTP is $otp. It expires in 60 minutes. If you did not request a OTP, no further action is required.";

        // Send SMS
        $response = $this->termiiService->sendSms($request->phone_number, $message);

        return $this->successResponse($response->message);
    }

    public function verifySmsOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:15', 'exists:users,phone_number', 'exists:mobile_verification_otps,phone_number'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user  = User::where('phone_number', $request->phone_number)->first();

        $this->checkIfUserCanVerifyThisOtp($user);

        $record = DB::table('mobile_verification_otps')->where('phone_number', $request->phone_number)->first();

        // check otp hash
        // check if expired
        $this->checkOtpValidity($request, $record);

        DB::table('mobile_verification_otps')->where('phone_number', $request->phone_number)->delete();

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

        // check if you are trying to resend otp
        $count = DB::table('email_verification_otps')->where('email', $request->email)->count();
        if ($count) {
            $record = DB::table('email_verification_otps')->where('email', $request->email)->first();

            $lastTime = $record->created_at;

            $timeDifference = Carbon::now()->timestamp - strtotime($lastTime);

            // wait one minute before retrying
            if ($timeDifference < 60) {
                return $this->failureResponse('Please wait before retrying.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // delete old otp
            DB::table('email_verification_otps')->where('email', $request->email)->delete();
        }

        // generate random 6 digit value
        $otp = rand(100000, 999999);

        try {
            // save new OTP to databse
            DB::table('email_verification_otps')->insert([
                'email' => $request->email,
                'otp' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            throw $e;
        }

        // send otp to email
        Mail::to($request->email)->send(new SendVerificationOtp($otp));

        return $this->successResponse("OTP sent to $request->email");
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email', 'exists:email_verification_otps,email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user  = User::where('email', $request->email)->first();

        $this->checkIfUserCanVerifyThisOtp($user);

        $record = DB::table('email_verification_otps')->where('email', $request->email)->first();

        // check otp hash
        // check if expired
        $this->checkOtpValidity($request, $record);

        DB::table('email_verification_otps')->where('email', $request->email)->delete();

        $user->markEmailAsVerified();

        return $this->successResponse("Email verified");
    }
}
