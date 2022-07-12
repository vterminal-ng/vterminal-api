<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Services\FlutterwaveService;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerificationController extends Controller
{
    use ApiResponder;

   
    public function verifyBvn(Request $request) {
       

    }
}
