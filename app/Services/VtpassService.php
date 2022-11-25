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
        $this->basicToken = $this->generateBasicToken();
    }

    public function purchaseAirtime($requestId, $serviceId, $amount, $phoneNo) {
        $response = $this->performVtRequest(
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
        
        $response = $this->performVtRequest(
            'POST',
            "/merchant-verify",
            [
                'billersCode' => $meterNo,
                'serviceID' => $operator,
                'type' => $meterType
            ],
        );

        $reponse = json_decode($response);
        dd($reponse);
        return $reponse;
    }
}