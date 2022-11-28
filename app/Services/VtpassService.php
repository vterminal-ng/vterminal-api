<?php

namespace App\Services;

use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use App\Traits\Generators;

class VtpassService
{
    use ConsumeExternalService, Generators, ApiResponder;

    protected $baseUri;
    protected $basicToken;

    public function __construct()
    {
        $this->baseUri = config('services.vtpass.base_url');
        $this->basicToken = $this->generateVtBasicToken();
    }

    public function purchaseAirtime($requestId, $serviceId, $amount, $phoneNo) {
        $response = $this->performBasicRequest(
            'POST',
            "/api/pay",
            [
                "request_id" => $requestId,
                "serviceID" => $serviceId,
                "amount" => $amount,
                "phone" => $phoneNo,
            ],
        );

        $reponse = json_decode($response);

        return $reponse;
    }

    public function verifyElectricityMeter($meterType, $meterNo, $operator) {
        
        $response = $this->performBasicRequest(
            'POST',
            "/api/merchant-verify",
            [
                'billersCode' => $meterNo,
                'serviceID' => $operator,
                'type' => $meterType
            ],
        );

        $reponse = json_decode($response);
        
        return $reponse;
    }

    public function makeElectricityPayment($requestId, $meterType, $meterNo, $amount, $operator) {
        
        $response = $this->performBasicRequest(
            'POST',
            "/api/pay",
            [
                'request_id' => $requestId,
                'serviceID' => $operator,
                'billersCode' => $meterNo,
                'variation_code' => $meterType,
                'amount' => $amount,
                'phone' => auth()->user()->phone_number
            ],
        );

        $reponse = json_decode($response);
        
        return $reponse;
    }
}