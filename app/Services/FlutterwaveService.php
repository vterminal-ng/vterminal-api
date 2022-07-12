<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Verification;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FlutterwaveService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $secret;

    public function __construct()
    {
        $this->baseUri = config('services.flwave.base_url');
        $this->secret = config('services.flwave.secret');
    }

    /**
     * get user details from BVN
     *
     * @param  User $user
     * @return object
     */
    public function getValidBvnData() {
        $response = $this->performRequest('POST', '/v3/kyc/bvns/123456789');
        dd($response);
        $bvnData = json_decode((string)$response);


        return $bvnData;
    }

}