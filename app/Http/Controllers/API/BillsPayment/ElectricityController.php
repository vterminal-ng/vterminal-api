<?php

namespace App\Http\Controllers\API\BillsPayment;

use App\Constants\BillsPayment;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VtpassService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ElectricityController extends Controller
{
    use ApiResponder, Generators;
    
    protected $vtpassService;
    
    public function __construct(VtpassService $vtpassService)
    {
        $this->vtpassService = $vtpassService;
    }
    
    public function verifyMeter(Request $request) {
        $request->validate([
            'operator_id' => ['required', 'string'],
            'meter_number' => ['required'],
            'meter_type' => ['required', 'string'],
        ]);

        $rep = $this->vtpassService->verifyService($request->meter_type, $request->meter_number, $request->operator_id);

        if (isset($rep->content->error)) {
            return $this->failureResponse("Cannot Verify Meter!", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse(
            "Meter Verification Successful",
            [
                "meterDetails" => $rep->content
            ], Response::HTTP_OK
        );
    }

    public function payElectricity(Request $request) {
        $request->validate([
            'operator_id' => ['required', 'string'],
            'meter_number' => ['required'],
            'meter_type' => ['required', 'string'],
            'amount' => ['required', 'integer'],
        ]);

        $user = User::find(auth()->id());
        
        if ($request->amount <= 0) {
            return $this->failureResponse(
                "Invalid Amount Entered",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $type = BillsPayment::ELECTRICITY;
        $reqId = $this->generateRequestID($type);
        
        // Debit user
        $user->walletWithdraw($request->amount);

        $rep = $this->vtpassService->makeVtPayment($reqId, $request->meter_type, $request->meter_number, $request->amount, $request->operator_id, $user->phone_number);
        
        if($rep) {
            if (isset($rep->content->error)) {
                return $this->failureResponse(
                    "We cannot process request at the moment.", 
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            } else {
                if ($rep->content->transactions->status === "delivered") {
                    // store transaction info
                    // store electricity purchase info

                    // send customer email

                    return $this->successResponse(
                        "Electricity Purchase Successful",
                        NULL,
                        Response::HTTP_OK
                    );
                } else {
                    // store transaction info
                    // store electricity purchase info

                    return $this->successResponse(
                        "Electricity Purchase is Processing",
                        NULL,
                        Response::HTTP_OK
                    );
                }
            }
        } else {
            return $this->failureResponse(
                "Something went wrong!",
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }
}
