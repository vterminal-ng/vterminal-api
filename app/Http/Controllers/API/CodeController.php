<?php

namespace App\Http\Controllers\API;

use App\Constants\CodeStatus;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CodeResource;
use App\Models\Code;
use App\Models\User;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class CodeController extends Controller
{
    use ApiResponder, Generators;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function transactionSummary(Request $request)
    {
        // validate amount and is_card_charged
        $request->validate([
            'amount' => ['required', 'integer'],
            "transaction_type" => ['required', 'in:withdrawal,deposit'],
            'charge_from' => ['required', 'in:cash,card'],
        ]);

        // calculate charge
        $charge = 100;
        if ($request->amount > 10000) {
            $charge = ceil($request->amount / 10000) * 100;
        }

        // subtotal
        $subtotal = $request->amount;

        // total
        if ($request->charge_from == 'card')
            $total = $request->amount + $charge;
        else
            $total = $subtotal;

        // is card charged

        //return
        return $this->successResponse("Transaction summary retrieved", [
            'transactionType' => $request->transaction_type,
            'subtotal' => (int)$request->amount,
            'charge' => (int)$charge,
            'total' => (int)$total,
            'chargeFrom' => $request->charge_from
        ]);
    }

    public function generateCode(Request $request)
    {
        $request->validate([
            "transaction_type" => ['required', 'in:withdrawal,deposit'],
            "subtotal_amount" => ['required', 'integer'],
            "charge_amount" => ['required', 'integer'],
            "total_amount" => ['required', 'integer'],
            'charge_from' => ['required', 'in:cash,card'],
            'pin' => ['required'],
        ]);


        $user = auth()->user();

        //TODO: Check if user has pending code already

        // check if pin is valid, if it isnt, it throw the InvalidTransactionPin Exception
        $user->validatePin($request->pin);


        // gen 6 digit random code
        $transactionCode = $this->generateTransCode();

        //TODO: check if code already exists, if it does, send failure response

        // gen 16 digit transaction reference
        $reference = $this->generateReference();
        //save code

        $code = Code::create([
            'customer_id' => $user->id,
            'code' => $transactionCode,
            'transaction_type' => $request->transaction_type,
            'subtotal_amount' => $request->subtotal_amount,
            'total_amount' => $request->total_amount,
            'charge_amount' => $request->charge_amount,
            'charge_from' => $request->charge_from,
            'reference' => $reference,
        ]);

        return $this->successResponse("Generated code successfully", new CodeResource($code));
    }

    public function activateCode(Request $request)
    {
        // validate pystack ref
        $request->validate([
            'paystack_reference' => ['required'],
            'transaction_code' => ['required', 'exists:codes,code']
        ]);

        // verify paystack transaction
        $paystackResponse = $this->paystackService->verifyTransaction($request->paystack_reference);

        // if payment failed, return
        if ($paystackResponse->data->status == "failed") {
            return $this->failureResponse("Payment failed, kindly retry.", Response::HTTP_BAD_REQUEST);
        }

        $code = Code::where('code', $request->transaction_code)->first();

        // if transaction code status is \anything other than PENDING, then it is invalid,
        // we can't activate code that isn't pending
        if ($code->status != CodeStatus::PENDING) {
            // refund payment
            $this->paystackService->refundTransaction($request->paystack_reference);
            return $this->failureResponse("Invalid transaction code", Response::HTTP_BAD_REQUEST);
        }

        // activate code
        $code->forceFill([
            'status' => CodeStatus::ACTIVE
        ])->save();

        // return 
        return $this->successResponse('Code activated successfully', ['code' => $request->transaction_code]);
    }

    public function activateCodeWithSavedCard(Request $request)
    {
        // validate pystack ref
        $request->validate([
            'paystack_auth_code' => ['required'],
            'transaction_code' => ['required', 'exists:codes,code']
        ]);

        $code = Code::where('code', $request->transaction_code);

        // if transaction code status is \anything other than PENDING, then it is invalid,
        // we can't activate code that isn't pending
        if ($code->status != CodeStatus::PENDING) {
            // refund payment
            return $this->failureResponse("Invalid transaction code", Response::HTTP_BAD_REQUEST);
        }

        // authorize authcode
        $this->authorize('activateWithSavedCard', $code);

        $totalAmountInKobo = $code->total_amount * 100;
        $response = $this->paystackService->chargeAuthorization(
            $code->customer->email,
            $totalAmountInKobo,
            $code->customer->authorizedCard->authorization_code,
            $this->generateReference()
        );

        // if transaction fialed, return falure
        if ($response->data->status == "failed") {
            return $this->failureResponse("Activation failed! reason: $response->data->status", Response::HTTP_OK);
        }

        // activate code
        $code->forceFill([
            'status' => CodeStatus::ACTIVE
        ])->save();

        // return 
        return $this->successResponse('Code activated successfully', ['code' => $request->transaction_code]);
    }

    public function cancelCode(Request $request)
    {
        $request->validate([
            'transaction_code' => ['required', 'exists:codes,code'],
            'pin' => ['required'],
        ]);

        $code = Code::where('code', $request->transaction_code);

        $this->authorize('cancel', $code);

        $code->customer()->validatePin($request->pin);

        if ($code->status != CodeStatus::PENDING || $code->status != CodeStatus::ACTIVE) {
            return $this->failureResponse('This code is already canclled or completed', Response::HTTP_BAD_REQUEST);
        }

        if ($code->status == CodeStatus::ACTIVE) {
            // deposit user wallet
            $code->customer()->deposit($code->total_amount);

            // cancel code
            $code->forceFill([
                'status' => CodeStatus::CANCELLED
            ])->save();

            // return
            return $this->successResponse('Code cancelled successfully and wallet has been credited');
        }

        // cancel code
        $code->forceFill([
            'status' => CodeStatus::CANCELLED
        ])->save();

        // return
        return $this->successResponse('Code cancelled successfully');
    }
}
