<?php

namespace App\Http\Controllers\API\BillsPayment;

use App\Constants\BillsPayment;
use App\Http\Controllers\Controller;
use App\Models\BillPayments;
use App\Models\User;
use App\Services\VtpassService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CableSubsController extends Controller
{
    use ApiResponder, Generators;
    
    protected $vtpassService;
    
    public function __construct(VtpassService $vtpassService)
    {
        $this->vtpassService = $vtpassService;
    }
    
    public function verifySmartCard(Request $request) {
        $request->validate([
            'service_id' => ['required', 'alpha'],
            'smartcard_no' => ['required'],
        ]);

        $verifyInfo = $this->vtpassService->verifyService(NULL, $request->smartcard_no, $request->service_id);

        if (isset($verifyInfo->content->error)) {
            return $this->failureResponse("Verification Unsuccessful!", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse(
            "Decoder Verified",
            [
                $verifyInfo->content
            ], Response::HTTP_OK
        );
    }

    public function getVariations($serviceId) {
        $variations = $this->vtpassService->getServiceVariations($serviceId);

        if (isset($variations->content->error)) {
            return $this->failureResponse("No service found!", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse(
            "Decoder Variations",
            [
                $variations['content']['varations']
            ], Response::HTTP_OK
        );
    }

    public function processTvSub(Request $request) {
        $request->validate([
            'service_id' => ['required', 'alpha'],
            'smartcard_no' => ['required'],
            'purchase_type' => ['nullable', 'required_unless:service_id,showmax', 'in:change,renew'],
            'variation_code' => ['required_if:purchase_type,change'],

        ]);

        $user = User::find(auth()->id());

        $type = BillsPayment::CABLETV;
        $reqId = $this->generateRequestID($type);
        
        if ($request->service_id !== "showmax") {
            $verifyInfo = $this->vtpassService->verifyService(NULL, $request->smartcard_no, $request->service_id);
            if (isset($verifyInfo->content->error)) {
                return $this->failureResponse("Error: ". $verifyInfo->content->error,
                Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }


        if (($request->service_id === "gotv" || $request->service_id === "dstv") && $request->purchase_type === 'renew') {
            $variationAmount = $verifyInfo->content->Renewal_Amount;
            $variationCode = $verifyInfo->content->Current_Bouquet_Code;
            $variationName = $verifyInfo->content->Current_Bouquet;
        } else {
            // get variation info
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
        }
        
        // Debit user
        $user->walletWithdraw($variationAmount);

        switch($request->service_id) {
            case "gotv":
            case "dstv":
            case "startimes":
                $rep = $this->vtpassService->makeVtPayment($reqId, $variationCode, $request->smartcard_no, $variationAmount, $request->service_id, $user->phone_number, $request->purchase_type);
                break;
            case "showmax":
                $rep = $this->vtpassService->makeVtPayment($reqId, $variationCode, $request->smartcard_no, $variationAmount, $request->service_id, $request->smartcard_no);
                break;
            default:
                return "Invalid type";
                break;
        }

        if (isset($rep->content->errors)) {
            return $this->failureResponse(
                "Error: An error occured. Contat admin.", Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Save purchase details
        BillPayments::create([
            'user_id' => $user->id,
            'status' => 'successful',
            'service_id' => $request->service_id,
            'service_name' => $variationName,
            'billers_code' => $request->smartcard_no,
            'request_id' => $reqId,
            'transaction_id' => $rep->content->transactions->transactionId,
            'amount' => $rep->content->transactions->amount,
        ]);

        // Save transaction details

        return $this->successResponse(
            "TV Payment Successful",
            [
                'subscription' => $variationName,
                'amount' => $rep->content->transactions->amount,
            ],
            Response::HTTP_OK
        );

    }
}
