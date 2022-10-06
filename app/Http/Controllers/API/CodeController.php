<?php

namespace App\Http\Controllers\API;

use App\Constants\ChargeFrom;
use App\Constants\CodeStatus;
use App\Constants\PaymentMethod;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CodeResource;
use App\Http\Resources\WalletTransactionResource;
use App\Models\Code;
use App\Models\User;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CodeController extends Controller
{
    use ApiResponder, Generators;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function customerTransactionCodes(Request $request)
    {
        $query = (new Code)->query()->latest();

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->type)) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $codes = $query->where('customer_id', auth()->id())->get();

        return $this->successResponse("Retrived your codes", CodeResource::collection($codes));
    }

    public function customerTransactionCode($codeReference)
    {
        $code = Code::where('reference', $codeReference)->firstOrFail();

        return $this->successResponse("Retrived your code details", new CodeResource($code));
    }

    public function transactionSummary(Request $request)
    {
        // validate amount and is_card_charged
        $request->validate([
            'amount' => ['required', 'integer'],
            'transaction_type' => ['required', 'in:' . TransactionType::VDEPOSIT . ',' . TransactionType::VWITHDRAWAL],
            'payment_method' => ['nullable', 'string', 'required_if:transaction_type,' . TransactionType::VWITHDRAWAL, 'in:' . PaymentMethod::all()],
            'charge_from' => ['required', 'in:' . ChargeFrom::CASH . ',' . ChargeFrom::CARD],
            'use_saved_bank' => ['required_if:transaction_type,' . TransactionType::VDEPOSIT, 'boolean'],
        ]);

        $summary = [];

        // calculate charge
        switch ($request->transaction_type) {
            case TransactionType::VWITHDRAWAL:
                $charge = 100;
                if ($request->amount > 10000) {
                    $charge = ceil($request->amount / 10000) * 100;
                }
                break;
            case TransactionType::VDEPOSIT:
                $charge = 50;
                if ($request->amount > 10000) {
                    $charge = ceil($request->amount / 10000) * 50;
                }
        }
        // subtotal
        $subtotal = $request->amount;

        // total
        if ($request->charge_from == ChargeFrom::CARD || $request->transaction_type == TransactionType::VDEPOSIT)
            $total = $request->amount + $charge;
        else
            $total = $subtotal;

        $summary['transactionType'] = $request->transaction_type;
        $summary['PaymentMethod'] = $request->payment_method ?? null;
        $summary['subtotal'] = (int)$subtotal;
        $summary['charge'] = (int)$charge;
        $summary['total'] = (int)$total;
        $summary['chargeFrom'] = $request->charge_from;

        // Adding deposit specific fields
        if ($request->transaction_type == TransactionType::VDEPOSIT) {
            $user = auth()->user();
            $summary['accountName'] = $user->bankDetail->account_name;
            $summary['accountNumber'] = $user->bankDetail->account_number;
            $summary['bankName'] = $user->bankDetail->bank_name;
            $summary['bankCode'] = $user->bankDetail->bank_code;
            $summary['transferRecipientCode'] = $user->bankDetail->recipient_code;
            $summary['chargeFrom'] = ChargeFrom::CASH;

            // if we are not using the bank details saved in our profile, then we generate new transfer recipient code
            if ($request->use_saved_bank == false) {
                $request->validate([
                    'bank_name' => ['required'],
                    'account_name' => ['required'],
                    'account_number' => ['required'],
                    'bank_code' => ['required'],
                ]);
                $response = $this->paystackService->createTranferRecipient(
                    $request->account_name,
                    $request->account_number,
                    $request->bank_code,
                    metadata: [
                        'user_id' => auth()->id(),
                    ]
                );

                $summary['transferRecipientCode'] = $response->data->recipient_code;
                $summary['accountName'] = $request->account_name;
                $summary['accountNumber'] = $request->account_number;
                $summary['bankName'] = $request->bank_name;
                $summary['bankCode'] = $request->bank_code;
            }
        }

        //return
        return $this->successResponse("Transaction summary retrieved", $summary);
    }

    public function generateCode(Request $request)
    {
        $request->validate([
            'transaction_type' => ['required', 'in:' . TransactionType::VDEPOSIT . ',' . TransactionType::VWITHDRAWAL],
            'payment_method' => ['nullable', 'string', 'required_if:transaction_type,' . TransactionType::VWITHDRAWAL, "in:" . PaymentMethod::all()],
            "subtotal_amount" => ['required', 'integer'],
            "charge_amount" => ['required', 'integer'],
            "total_amount" => ['required', 'integer'],
            'charge_from' => ['required', 'in:' . ChargeFrom::CASH . ',' . ChargeFrom::CARD],
            'pin' => ['required'],
        ]);

        $user = User::find(auth()->id());

        //TODO: Check if user has pending code already

        // check if pin is valid, if it isnt, it throw the InvalidTransactionPin Exception
        $user->validatePin($request->pin);

        $customerCodes = $user->customerCodes()->get();
        dd(count($user->customerCodes()->where('status', CodeStatus::ACTIVE)->get()));
        // return failure if user has 1 active or pending code
        if ($customerCodes->where('status', CodeStatus::PENDING)->orWhere('status', CodeStatus::ACTIVE)->get()->count()) {
            return $this->failureResponse("You have an unused or pending transaction code, kindly use the code or cancel before creating a new one", Response::HTTP_BAD_REQUEST);
        }

        $params = [];

        // gen 6 digit random code
        $transactionCode = $this->generateTransCode();

        //TODO: check if code already exists, if it does, send failure response

        // gen 16 digit transaction reference
        $reference = $this->generateReference($request->transaction_type);

        $params['customer_id'] = $user->id;
        $params['code'] = $transactionCode;
        $params['transaction_type'] = $request->transaction_type;
        $params['subtotal_amount'] = $request->subtotal_amount;
        $params['total_amount'] = $request->total_amount;
        $params['charge_amount'] = $request->charge_amount;
        $params['charge_from'] = $request->charge_from;
        $params['reference'] = $reference;
        $params['payment_method'] = $request->payment_method ?? null;

        if ($request->transaction_type == TransactionType::VDEPOSIT) {
            $request->validate([
                'transfer_recipient_code' => ['required'],
                'bank_name' => ['required'],
                'account_name' => ['required'],
                'account_number' => ['required'],
                'bank_code' => ['required'],
            ]);

            // check if recipient code exists
            $response = $this->paystackService->fetchTranferRecipient($request->transfer_recipient_code);

            if (!$response->status || $response->data->metadata->user_id != auth()->id()) {
                return $this->failureResponse("Invalid transfer recipient code", Response::HTTP_BAD_REQUEST);
            }

            $params['paystack_transfer_recipient_code'] = $request->transfer_recipient_code;
            $params['account_name'] = $request->account_name;
            $params['account_number'] = $request->account_number;
            $params['bank_name'] = $request->bank_name;
            $params['bank_code'] = $request->bank_code;
            $params['status'] = CodeStatus::ACTIVE; // Activating the deposit transaction code
        }
        //save code

        $code = Code::create($params);


        return $this->successResponse("Generated code successfully", new CodeResource($code->fresh()));
    }

    public function activateCode(Request $request)
    {
        // validate pystack ref
        $request->validate([
            'reference' => ['required', 'exists:codes,reference'],
            'pin' => ['required'],
        ]);

        $code = Code::where('reference', $request->reference)->first();

        // dd($code->customer->email);

        $code->customer->validatePin($request->pin);


        // if transaction code status is \anything other than PENDING, then it is invalid,
        // we can't activate code that isn't pending
        if ($code->status != CodeStatus::PENDING || $code->transaction_type != TransactionType::VWITHDRAWAL) {
            // refund payment
            $this->paystackService->refundTransaction($request->paystack_reference);
            return $this->failureResponse("Invalid transaction code", Response::HTTP_BAD_REQUEST);
        }

        $totalAmountInKobo = ($code->total_amount + $this->paystackService->calculateApplicableFee($code->total_amount)) * 100;

        switch ($code->payment_method) {
            case PaymentMethod::WALLET:
                $withdrawResponse = $code->customer->walletWithdraw($code->total_amount);

                // activate code
                $code->forceFill([
                    'status' => CodeStatus::ACTIVE
                ])->save();

                return $this->successResponse("Your transaction code $request->transaction_code has been activated successfully", new WalletTransactionResource($withdrawResponse));
                break;
            case PaymentMethod::NEW_CARD:

                $response = $this->paystackService->initializeTransaction($code->customer->email, $totalAmountInKobo, $code->reference, $code->transaction_type);

                // return 
                return $this->successResponse("Payment page URL generated for trancastion code $request->transaction_code", $response->data);
                break;
            case PaymentMethod::SAVED_CARD:
                if (!$code->customer->authorizedCard) {
                    return $this->failureResponse("You do not have a saved card yet", Response::HTTP_BAD_REQUEST);
                }

                $response = $this->paystackService->chargeAuthorization(
                    $code->customer->email,
                    $totalAmountInKobo,
                    $code->customer->authorizedCard->authorization_code,
                    $this->generateReference($code->transaction_type)
                );

                if ($response->data->status == "failed") {
                    return $this->failureResponse("Activation failed! reason: $response->data->status", Response::HTTP_OK);
                }

                // activate code
                $code->forceFill([
                    'status' => CodeStatus::ACTIVE
                ])->save();

                // return 
                return $this->successResponse('Code activated successfully', ['code' => new CodeResource($code)]);

                break;

            default:
                return $this->failureResponse("The payment method \"$code->payment_method\" is invalid", Response::HTTP_UNPROCESSABLE_ENTITY);
                break;
        }
    }

    public function cancelCode(Request $request)
    {
        $request->validate([
            'reference' => ['required', 'exists:codes,reference'],
            'pin' => ['required'],
        ]);

        $code = Code::where('reference', $request->reference)->first();

        if (auth()->id() !== $code->customer_id)
            throw new AuthorizationException();

        $code->customer->validatePin($request->pin);

        if ($code->status != CodeStatus::PENDING && "$code->status" != CodeStatus::ACTIVE) {
            return $this->failureResponse('This code is already canclled or completed', Response::HTTP_BAD_REQUEST);
        }

        if ($code->status == CodeStatus::ACTIVE && $code->transaction_type == TransactionType::VWITHDRAWAL) {
            // deposit user wallet
            $code->customer->walletDeposit($code->total_amount);

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

    ##########################################################
    # Merchants side of the story begins
    ##########################################################

    public function codeSummary(Request $request)
    {
        // validate
        $request->validate([
            'code' => ['required'],
        ]);

        // get code details
        $code = Code::where('code', $request->code)->first();

        if (!$code) {
            return $this->failureResponse('Code not found', Response::HTTP_NOT_FOUND);
        }

        // return details
        return $this->successResponse("Code Summary", new CodeResource($code));
    }

    public function resolveCode(Request $request)
    {
        // validate
        $request->validate([
            'code' => ['required'],
        ]);

        // get code details
        $code = Code::where('code', $request->code)->first();

        //get merchant
        $merchant = User::find(auth()->id());

        if (!$code) {
            return $this->failureResponse('Code not found', Response::HTTP_NOT_FOUND);
        }

        if ($code->status != CodeStatus::ACTIVE) {
            return $this->failureResponse('This code is not activated. Kindly let the customer know.', Response::HTTP_BAD_GATEWAY);
        }



        switch ($code->transaction_type) {
            case TransactionType::VWITHDRAWAL:
                $merchant->walletDeposit($code->total_amount);

                break;

            case TransactionType::VDEPOSIT:
                // Lowest transfer charge is 10 naira for tansations of 5000 and below 
                $charge = 10;
                $amount = $code->total_amount;

                if (($amount > 5000) && ($amount <= 50000)) $charge = 25;

                if ($amount > 50000) $charge = 50;

                $amountAndCharge = $amount + $charge;

                $totalAmountInKobo = $amountAndCharge * 100;
                // withdraw from merchant wallet
                $merchant->walletWithdraw($amountAndCharge);

                // transfer to users account
                $response = $this->paystackService->initiateTransfer($totalAmountInKobo, $code->paystack_transfer_recipient_code);
        }
        $code->forceFill([
            'merchant_id' => $merchant->id,
            'status' => CodeStatus::COMPLETED
        ])->save();
        // return details
        return $this->successResponse("Trasaction complete", new CodeResource($code));
    }
}
