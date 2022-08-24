<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Verification;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NubanService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $secret;

    public function __construct()
    {
        $this->baseUri = config('services.nuban.base_url');
        $this->secret = config('services.nuban.api_key');
    }

    /**
     * get user details from BVN
     *
     * @return object
     */
    public function getBankCodes() {
        $response = $this->performRequest('GET', '/bank_codes.json');
        //dd($response);
        $bankCodes = json_decode((string)$response);

        $bankCodes = $bankCodes[2];

        return $bankCodes;
    }

    public function getBankDetails($accountNo) {
        //dd($accountNo);
        $response = $this->performRequest('GET', "/api/$this->secret?acc_no=$accountNo");
        // dd($response);
        $details = json_decode((string)$response);

        return $details;
    }

}