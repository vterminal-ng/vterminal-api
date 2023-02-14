<?php

namespace App\Http\Controllers\API\BillsPayment;

use App\Constants\BillsPayment;
use App\Http\Controllers\Controller;
use App\Models\BillPayments;
use App\Models\User;
use App\Services\VtpassService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DataController extends Controller
{
    use ApiResponder, Generators;

    protected $vtpassService;

    public function __construct(VtpassService $vtpassService)
    {
        $this->vtpassService = $vtpassService;
    }

    // get networks
    public function getNetworks() {}

    public function getDataPlans(Request $request, $serviceId) {
        
        $rep = $this->vtpassService->getServiceVariations($serviceId);

        if (isset($rep['content']['errors'])) {
            return $this->failureResponse(
                "Service unavailable", Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        
        return $this->successResponse(
            "Data Plans",
            $rep['content']['varations'],
            Response::HTTP_OK
        );

    }

    public function buyData(Request $request) {
        $request->validate([
            'service_id' => ['required'],
            'variation_code' => ['required'],
            'phone_number' => ['required', 'size:11'],
        ]);

        $user = User::find(auth()->id());

        $type = BillsPayment::DATA;
        $reqId = $this->generateRequestID($type);

        // return network data plans
        $variation = $this->vtpassService->getServiceVariations($request->service_id);
        if (isset($variation->content->errors)) {
            return $this->failureResponse(
                "Service unavailable", Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $variationCodes = $variation['content']['varations'];
        $variationCode = $request->variation_code;
        $variationCodesOutput = array_column($variationCodes, 'variation_code');

        if (!in_array($variationCode, $variationCodesOutput))
        {
            return $this->failureResponse(
                "Error! Invalid User Selection", Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        
        $variationKey = array_search($variationCode, $variationCodesOutput);

        $variationAmount = $variationCodes[$variationKey]['variation_amount'];
        $variationName = $variationCodes[$variationKey]['name'];
        
        // Debit user
        $user->walletWithdraw($variationAmount);

        $rep = $this->vtpassService->makeVtPayment($reqId, $request->variation_code, $request->phone_number, $variationAmount, $request->service_id, $request->phone_number);
        if (isset($rep->content->errors)) {
            return $this->failureResponse(
                "Error: " . strtolower($rep->response_description), Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
       
        // Save purchase details
        BillPayments::create([
            'user_id' => $user->id,
            'status' => 'successful',
            'service_id' => $request->service_id,
            'service_name' => $variationName,
            'billers_code' => $request->phone_number,
            'request_id' => $reqId,
            'transaction_id' => $rep->content->transactions->transactionId,
            'amount' => $rep->content->transactions->amount,
        ]);

        // Save transaction details

        return $this->successResponse(
            "Data Purchase Successful",
            [
                'phoneNo' => $request->phone_number,
                'plan' => $variationName,
                'amount' => $rep->content->transactions->amount,
            ], Response::HTTP_OK
        );

    }
}
