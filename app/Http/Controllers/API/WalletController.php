<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use ApiResponder;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            "amount" => ['required', 'integer'],
            "recipient_cdoe" => ['required', 'integer']
        ]);

        // get user object of auth user
        $user = User::find(auth()->id());

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
            "reference" => ['required', 'string'],
        ]);

        // get user object of auth user

        // call paystack verify transction API

        // if transaction fialed, return falure

        // if transation was successful,get amount from the verification and deposit into wallet.

        // if AuthorizedCode not stored, store it in the Authorized_code table

        // return Success
    }

    public function depositWithSavedCard(Request $request)
    {
        $request->validate([
            "email" => ['required', 'email'],
            "authCode" => ['required', "string"],
            "amount" => ['required', 'integer']
        ]);

        // get user object of auth user

        // get authorized code for the authenticated user on the Authorized_code table

        // check the authorization with paystack's Check Authorization endpoint for sufficient funds

        // charge the card with paystacks Charge Authorization endpoint

        // if successful, deposit into wallet.

        // return success
    }
}
