<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;


class VerificationService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $secret;
    protected $key;

    public function __construct()
    {
        $this->baseUri = config('services.verify_me.base_url');
        $this->secret = config('services.verify_me.secret_key');
    }

    public function verifyBvn($bvn, $params)
    {
        $reponse = $this->performRequest('POST', "/v1/verifications/identities/bvn/$bvn", $params);

        $response = json_decode((string)$reponse);

        return $response;
    }

    public function verifyVin($vin, $dob, $params)
    {
        $reponse = $this->performRequest('POST', "/v1/verifications/identities/vin/$vin", [
            'dob' => $dob,
            'firstname' => $params['firstname'],
            'lastname' => $params['lastname']
        ]);
        // dd($reponse);
        $response = json_decode((string)$reponse);
        return $response;
    }

    public function verifyLicense($license, $params)
    {
        $reponse = $this->performRequest('POST', "/v1/verifications/identities/drivers_license/$license", $params);

        $response = json_decode((string)$reponse);

        return $response;
    }

    public function verifyPassport($passport, $params)
    {
        $reponse = $this->performRequest('POST', "/v1/verifications/identities/drivers_license/$passport", $params);

        $response = json_decode((string)$reponse);
        return $response;
    }

    public function verifyNin($nin, $params)
    {
        $reponse = $this->performRequest('POST', "/v1/verifications/identities/nin/$nin", $params);

        $response = json_decode((string)$reponse);

        return $response;
    }

    public function getAccountInfo($bankCode, $accountNo)
    {
        $reponse = $this->performRequest('GET', "/v1/banks/$bankCode/accounts/$accountNo");

        $response = json_decode((string)$reponse);

        return $response;
    }    
}
