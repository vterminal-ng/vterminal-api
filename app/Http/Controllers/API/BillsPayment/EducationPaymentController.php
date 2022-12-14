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

class EducationPaymentController extends Controller
{
    use ApiResponder, Generators;
    
    protected $vtpassService;
    
    public function __construct(VtpassService $vtpassService)
    {
        $this->vtpassService = $vtpassService;
    }

    public function getExamVariations($examId) {
        $variations = $this->vtpassService->getServiceVariations($examId);

        if (isset($variations->content->error)) {
            return $this->failureResponse("No service found!", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse(
            "Exam Variations - " . $examId,
            [
                $variations['content']['varations']
            ], Response::HTTP_OK
        );
    }

    public function verifyJambProfile(Request $request) {
        $request->validate([
            'exam_id' => ['required'],
            'variation_code' => ['required'],
            'jamb_profile_id' => ['required'],
        ]);

        $profileInfo = $this->vtpassService->verifyService($request->variation_code, $request->jamb_profile_id, $request->exam_id);

        if (isset($profileInfo->content->error)) {
            return $this->failureResponse("Unable to verify profile at this moment. Please try again.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse(
            "Jamb Profile Verified",
            [
                'name' => $profileInfo->content->Customer_Name
            ], Response::HTTP_OK
        );
    }

    public function makeExamPayment(Request $request) {
        $request->validate([
            'exam_id' => ['required'],
            'variation_code' => ['required'],
            'jamb_profile_id' => ['required_if:exam_id,jamb'],
            'phone_number' => ['required', 'size:11'],
        ]);

        $user = User::find(auth()->id());

        $type = BillsPayment::EXAM;
        $reqId = $this->generateRequestID($type);

        // Get variation amount
        $variation = $this->vtpassService->getServiceVariations($request->exam_id);
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
        // $user->walletWithdraw($variationAmount);

        switch($request->exam_id) {
            case "jamb":
                $rep = $this->vtpassService->makeVtPayment($reqId, $variationCode, $request->jamb_profile_id, $variationAmount, $request->exam_id, $request->phone_number);
                break;
            case "waec":
            case "waec-registration":   
                $rep = $this->vtpassService->makeVtPayment($reqId, $variationCode, NULL, $variationAmount, $request->exam_id, $request->phone_number);
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

        $voucherPin = $rep->Pin ?? $rep->tokens[0] ?? $rep->cards[0]->Pin;
        $serial = $rep->cards[0]->Serial ?? NULL;

        // Save purchase details
        BillPayments::create([
            'user_id' => $user->id,
            'status' => 'successful',
            'service_id' => $request->exam_id,
            'service_name' => $variationName,
            'billers_code' => $request->jamb_profile_id ?? NULL,
            'request_id' => $reqId,
            'transaction_id' => $rep->content->transactions->transactionId,
            'amount' => $rep->content->transactions->amount,
        ]);

        // Save transaction details -- TODO

        return $this->successResponse(
            "Educatin Payment Successful",
            [
                'exam' => strtoupper($request->exam_id),
                'type' => $variationName,
                'cardPin' => $voucherPin,
                'serialNo' => $serial,
                'amount' => $rep->content->transactions->amount,
            ],
            Response::HTTP_OK
        );
    }
}
