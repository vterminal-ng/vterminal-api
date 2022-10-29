<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Illuminate\Support\Facades\Http;

class DojahVerifyService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $privateKey;
    protected $pubKey;
    protected $appId;

    public function __construct()
    {
        $this->baseUri = config('services.dojah.base_url');
        $this->privateKey = config('services.dojah.private_key');
        $this->pubKey = config('services.dojah.public_key');
        $this->appId = config('services.dojah.app_id');
    }

    public function lookupTinNo($tinNo) {
        $response = $this->makeRequest('get',
            "/api/v1/kyc/tin?tin=$tinNo", [],
            [
                'Authorization' => $this->privateKey,
                'AppId' => $this->appId,
                'accept' => 'text/plain',
            ]);
        
        $reponse = json_decode((string)$response);
        return $reponse;
    }

    public function lookupCacInfo($companyName, $rcNumber) {
        $response = $this->makeRequest('get', 
            "/api/v1/kyc/cac?rc_number=$rcNumber&company_name=$companyName", [], 
            [
                'Authorization' => $this->privateKey,
                'AppId' => $this->appId,
                'accept' => 'text/plain',
            ]);
        
        $reponse = json_decode((string)$response);
        return $reponse;
    }
}