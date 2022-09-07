<?php

namespace App\Http\Controllers\API;

use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorizedCardResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Http\Response;

class AuthorizedCardController extends Controller
{
    use ApiResponder, Generators;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function add(Request $request)
    {

        $user = auth()->user();

        if ($user->authorizedCard) {
            return $this->failureResponse("User already has a saved card", Response::HTTP_BAD_REQUEST);
        }

        $chargeAmountInKobo = 5000; // 50 NGN

        $response = $this->paystackService->initializeTransaction($user->email, $chargeAmountInKobo, $this->generateReference(), TransactionType::ADD_CARD);

        // return 
        return $this->successResponse("Payment page URL generated", $response->data);
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
