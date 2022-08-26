<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorizedCardResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use Illuminate\Http\Response;

class AuthorizedCardController extends Controller
{
    use ApiResponder;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function add(Request $request)
    {
        $request->validate([
            'paystack_reference' => ['required', 'string']
        ]);

        $user = User::find(auth()->id());

        // validate transaction ref
        $paystackResponse = $this->paystackService->verifyTransaction($request->paystack_reference);

        // dd($paystackResponse);


        if ($user->authorizedCard) {
            return $this->failureResponse("User already has a saved card", Response::HTTP_NOT_ACCEPTABLE);
        }

        // save card details
        $authorizedCard = $user->authorizedCard()->create([
            "authorization_code" => $paystackResponse->data->authorization->authorization_code,
            "card_type" => $paystackResponse->data->authorization->card_type,
            "last4" => $paystackResponse->data->authorization->last4,
            "exp_month" => $paystackResponse->data->authorization->exp_month,
            "exp_year" => $paystackResponse->data->authorization->exp_year,
            "bin" => $paystackResponse->data->authorization->bin,
            "bank" => $paystackResponse->data->authorization->bank,
            "signature" => $paystackResponse->data->authorization->signature,
            "account_name" => $paystackResponse->data->authorization->account_name,
            "reference" => $request->paystack_reference,
        ]);

        // refund transaction
        if ($paystackResponse->data->status == "success") {
            $this->paystackService->refundTransaction($request->paystack_reference);
        }

        // return success
        return $this->successResponse("Card saved successfully!", new AuthorizedCardResource($authorizedCard), Response::HTTP_CREATED);
    }

    public function getCard()
    {
        $user = User::find(auth()->id());

        return $this->successResponse("My card", new AuthorizedCardResource($user->authorizedCard));
    }

    public function delete()
    {
        $user = User::find(auth()->id());

        $user->authorizedCard()->delete();

        return $this->successResponse("deleted", code: Response::HTTP_NO_CONTENT);
    }
}
