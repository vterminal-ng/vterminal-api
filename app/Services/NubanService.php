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
    public function getBanks()
    {
        $response = $this->performRequest('GET', '/bank_codes.json');
        //dd($response);
        $bankCodes = json_decode((string)$response);

        //get the main data from the API response
        $bankCodes = $bankCodes[2];

        return $bankCodes;
    }

    public function getAccountDetails($accountNo, $bankCode)
    {
        //dd($accountNo);
        $response = $this->performRequest('GET', "/api/$this->secret?acc_no=$accountNo&bank_code=$bankCode");
        $details = json_decode((string)$response);

        $details = $details[0];

        return $details;
    }
}
