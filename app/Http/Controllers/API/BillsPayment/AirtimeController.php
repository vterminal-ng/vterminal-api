<?php

namespace App\Http\Controllers\API\BillsPayment;

use App\Constants\BillsPayment;
use App\Http\Controllers\Controller;
use App\Models\BillPayments;
use App\Models\User;
use App\Services\VtpassService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AirtimeController extends Controller
{
    use ApiResponder, Generators;

    protected $vtpassService;
    
    public function __construct(VtpassService $vtpassService) {
        $this->vtpassService = $vtpassService;
    }
        
 
    public function topup(Request $request) {
        $request->validate([
            'network_id' => ['required', 'integer'],
            'phone_no' => ['required', 'string', 'size:11'],
            'amount' => ['required', 'integer', 'min:100', 'max:5000'],
        ]);

        $user = User::find(auth()->id());
        
        if ($request->amount <= 0) {
            return $this->failureResponse(
                "Invalid Amount Entered",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Get sevice id from DB
        //$network = Network::findorFail($request->network_id);
        //$networkName = $network->service_id;

        $networkName = "glo";
        $type = BillsPayment::AIRTIME;
        $reqId = $this->generateRequestID($type);
        
        // Debit user
        $user->walletWithdraw($request->amount);

        $rep = $this->vtpassService->purchaseAirtime($reqId, $networkName, $request->amount, $request->phone_no);
        
        switch ($rep->code) {
            case "000":
                if ($rep->content->transactions->status === "delivered") {
                    BillPayments::create([
                        'user_id' => $user->id,
                        'status' => 'successful',
                        'service_id' => $networkName,
                        'service_name' => $rep->content->transactions->product_name,
                        'billers_code' => $request->phone_no,
                        'request_id' => $reqId,
                        'transaction_id' => $rep->content->transactions->transactionId,
                        'amount' => $rep->content->transactions->amount,
                    ]);

                    // TODO: create transaction record

                    return $this->successResponse(
                        "Airtime Purchase Successful",
                        NULL,
                        Response::HTTP_OK
                    );
                }
                break;
            case "099":
                return $this->successResponse(
                    "Airtime Purchase is Processing.",
                    Response::HTTP_OK
                );
            case "016":
            case "091":
                // Refund user if transaction fails
                $user->walletDeposit($request->amount);
                
                return $this->failureResponse(
                    "Airtime Purchase Failed. Please try again or contact admin",
                    Response::HTTP_BAD_REQUEST
                );
                break;
            default:
                break;
        }
    }
}
