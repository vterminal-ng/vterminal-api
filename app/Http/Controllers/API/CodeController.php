<?php

namespace App\Http\Controllers\API;

use App\Constants\CodeStatus;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
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
        $total = $request->amount + $charge;

        // is card charged

        //return
        return $this->successResponse("Transaction summary retrieved", [
            'transactionType' => $request->transaction_type,
            'subtotal' => $request->amount,
            'charge' => $charge,
            'total' => $total,
            'chargeFrom' => $request->charge_from
        ]);
    }

    public function generateCode(Request $request)
    {
        $request->validate([
            'customer' => ['email', 'exists:user,email'],
            "transaction_type" => ['required', 'in:withdrawal,deposit'],
            "subtotal_amount" => ['required', 'integer'],
            "charge_amount" => ['required', 'integer'],
            "total_amount" => ['required', 'integer'],
            'charge_from' => ['required', 'in:cash,card'],
            'pin' => ['required'],
        ]);

        $user = auth()->user();

        // A merchant can creat code on behalf of a customer by providing the customers email, 
        // the customer will, in turn, type in his/her pin
        if ($request->exists('customer')) {
            $user = User::where('email', $request->customer);

            // check if the user the merchant is helping to generate code has a verified profile
            if (!$user->isProfileVerified()) {
                return $this->failureResponse("This user has not verified their profile. Kindly verify profile before you proceed", Response::HTTP_BAD_REQUEST);
            }
        }

        // check if pin is valid, if it isnt, it throw the InvalidTransactionPin Exception
        $user->validatePin($request->pin);


        // gen 6 digit random code
        $transactionCode = $this->generateTransCode();

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

        return $this->successResponse("Generated code successfully", $code);
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
            $this->failureResponse("Invalid transaction code", Response::HTTP_BAD_REQUEST);

            // refund payment
            $this->paystackService->refundTransaction($request->paystack_reference);
        }

        // activate code
        $code->forceFill([
            'status' => CodeStatus::ACTIVE
        ])->save();

        // return 
        return $this->successResponse('Code activated successfully', ['code' => $request->transaction_code]);
    }
}
