<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\Generators;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    public function withdraw(Request $request)
    {
        $request->validate([
            "amount" => ['required', 'integer'],
            "recipient_cdoe" => ['required', 'integer']
        ]);

        // get user object of auth user

        // initialize transfer paystack request

        // get the transfer code from above request

        // finalize transfer paystack request

        // if transation was successful, withdraw from user wallet,

        // return Success
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
