<?php

namespace App\Http\Controllers\API;

use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WalletController extends Controller
{
    use ApiResponder, Generators;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            // "amount" => ['required'],
            "recipient_cdoe" => ['required', 'string']
        ]);

        // get user object of auth user
        $user = User::find(auth()->id());

        // TODO: Middle ware to avoid people who haveadd a payout account to perfor this request
        // initialize transfer paystack request
        $response = $this->paystackService->initiateTransfer($request->amount, $user->bankDetail->recipient_code);

        // get the transfer code from above request
        $transferCode = $response->data->transfer_code;

        // finalize transfer paystack request
        // TODO: Disable OTP in the paystack portal
        $response = $this->paystackService->finalizeTransfer($transferCode);

        // if transation was successful, withdraw from user wallet,
        $user->withdraw($request->amount);

        // return Success
        return $this->successResponse("Withdrawal Complete");
    }

    public function deposit(Request $request)
    {
        $request->validate([
            "amount" => ['required', 'min:500', 'integer'],
        ]);

        // get user object of auth user
        $user = auth()->user();

        // if transation was successful,get amount from the verification and deposit into wallet.
        $amountInKobo = $request->amount * 100;

        $response = $this->paystackService->initializeTransaction($user->email, $amountInKobo, $this->generateReference(), TransactionType::CREDIT_WALLET);

        // return Success
        return $this->successResponse("Payment page URL generated for wallet deposit", $response->data);
    }

    public function depositWithSavedCard(Request $request)
    {
        $request->validate([
            "authCode" => ['required', "string"],
            "amount" => ['required', 'integer']
        ]);

        // get user object of auth user
        $user = User::find(auth()->id());

        // charge the card with paystacks Charge Authorization endpoint
        $amountInKobo = $request->amount * 100;
        $response = $this->paystackService->chargeAuthorization($user->email, $amountInKobo, $request->authCode, $this->generateReference());

        // if transaction fialed, return falure
        if ($response->data->status == "failed") {
            return $this->failureResponse("Deposit failed! reason: $response->data->status", Response::HTTP_OK);
        }

        // if transation was successful,get amount from the verification and deposit into wallet.
        $amountToDeposit = $response->data->amount / 100;
        $user->deposit($amountToDeposit);

        // return success
        return $this->successResponse("Successfully deposited $amountToDeposit into wallet");
    }
}
