<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use GuzzleHttp\Exception\ClientException;

class TermiiService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = config('services.termii.base_url');
    }



    /**
     * send otp to a users phone number
     *
     * @param  User $user
     * @return object
     */
    public function sendOtp(User $user)
    {
        $response = $this->performRequest('POST', '/api/sms/otp/send', [
            "api_key" => config('services.termii.key'),
            "message_type" => config('services.termii.message_type'),
            "to" => $user->phone_number,
            "from" =>  config('services.termii.from'),
            "channel" => config('services.termii.channel'),
            "pin_attempts" => config('services.termii.pin_attempts'),
            "pin_time_to_live" =>  config('services.termii.pin_time_to_live'),
            "pin_length" => config('services.termii.pin_length'),
            "pin_placeholder" => config('services.termii.pin_placeholder'),
            "message_text" => config('services.termii.message_text'),
            "pin_type" => config('services.termii.pin_type')
        ]);


        $termiiVerificationData = json_decode((string) $response);

        // delete previous OTP data if any
        $user->otp->delete();

        // save otp details
        $user->otp->create([
            'pin_id' => $termiiVerificationData->pinId
        ]);


        return $termiiVerificationData;
    }

    /**
     * verifyOtp
     *
     * @param  User $user
     * @param  string $pinCode
     * @return object
     */
    public function verifyOtp(User $user, $pinCode)
    {
        $response = $this->performRequest('POST', '/api/sms/otp/verify', [
            "api_key" => config('services.termii.key'),
            "pin_id" => $user->otp->pin_id,
            "pin" => $pinCode
        ]);

        // delete previous OTP data if any
        $user->otp->delete();

        return json_decode((string) $response);
    }

    /**
     * sendSms
     *
     * @param  mixed $phoneNumber
     * @param  mixed $message
     * @return object
     */
    public function sendSms(string $phoneNumber, string $message)
    {

        $response = $this->performRequest('POST', '/api/sms/send', [
            "to" => $phoneNumber,
            "from" =>  config('services.termii.from'),
            "sms" => $message,
            "type" => config('services.termii.sms_type'),
            "channel" => config('services.termii.channel'),
            "api_key" => config('services.termii.key'),
        ]);

        return json_decode((string) $response);
    }
}
