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

    protected $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        $this->flutterwaveService = $flutterwaveService;
    }

    public function verifyBvn(Request $request) {
        $request->validate([
            "bvn_number" => ['required', 'size:11', 'string']
        ]);

        // $user = Verification::where('user_id', auth()->id());
        // if($user) {
        //     return $this->failureResponse("User already verified", Response::HTTP_UNPROCESSABLE_ENTITY);
        // }


    // Send SMS
        $response = $this->flutterwaveService->getValidBvnData($request->bvn_number);

        dd($response);

    }
}
