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

    public function verifyService($type, $billerCode, $serviceId) {
        
        $response = $this->performBasicRequest(
            'POST',
            "/api/merchant-verify",
            [
                'billersCode' => $billerCode,
                'serviceID' => $serviceId,
                'type' => $type
            ],
        );

        $reponse = json_decode($response);
        
        return $reponse;
    }

    public function makeVtPayment($requestId, $variationCode, $billersCode, $amount, $serviceId, $phone, $subType = NULL) {
        
        $response = $this->performBasicRequest(
            'POST',
            "/api/pay",
            [
                'request_id' => $requestId,
                'serviceID' => $serviceId,
                'billersCode' => $billersCode,
                'variation_code' => $variationCode,
                'amount' => $amount,
                'phone' => $phone,
                'subscription_type' => $subType,
            ],
        );

        $reponse = json_decode($response);
        
        return $reponse;
    }

    public function getServiceVariations($serviceId) {
        
        $response = $this->performBasicRequest(
            'GET',
            "/api/service-variations?serviceID=$serviceId"
        );

        $reponse = json_decode($response, true);
        
        return $reponse;
    }
}