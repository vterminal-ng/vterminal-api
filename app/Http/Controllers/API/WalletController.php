<?php

namespace App\Http\Controllers\API;

use App\Constants\PaymentMethod;
use App\Constants\RewardAction;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Merchant;
use App\Http\Resources\WalletTransactionResource;
use App\Models\MerchantDetail;
use App\Models\User;
use App\Notifications\Deposit;
use App\Notifications\Withdraw;
use App\Services\PaystackService;
use App\Services\SquadcoService;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WalletController extends Controller
{
    use ApiResponder, Generators;

    protected $paystackService;
    protected $squadcoService;

    public function __construct(PaystackService $paystackService, SquadcoService $squadcoService)
    {
        $this->paystackService = $paystackService;
        $this->squadcoService = $squadcoService;
    }

    public function getTransactions(Request $request)
    {
        $user = User::find(auth()->id());

        return $this->successResponse("Retrieved wallet transactions", WalletTransactionResource::collection($user->transactions)->sortByDesc('created_at')->values()->all());
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            "amount" => ['required'],
        ]);
        // get user object of auth user
        $user = User::find(auth()->id());

        if (!$user->bankDetail) {
            return $this->failureResponse("Kindly add a bank account for payout", Response::HTTP_BAD_REQUEST);
        }

        // if transation was successful, withdraw from user wallet,
        $user->walletWithdraw($request->amount);

        // TODO: Middle ware to avoid people who haven't added a payout account to perform this request
        // initialize transfer paystack request
        $amountInKobo = $request->amount * 100;
        $response = $this->paystackService->initiateTransfer($amountInKobo, $user->bankDetail->recipient_code, TransactionType::PAYOUT);

        if (!$response->status) {
            // reversing the transaction because the paystack transfer failed
            $user->walletDeposit($request->amount);
            return $this->failureResponse("Withdrawal failed, Reason: $response->message", Response::HTTP_BAD_REQUEST);
        }
        // get the transfer code from above request
        // $transferCode = $response->data->transfer_code;

        // finalize transfer paystack request
        // TODO: Disable OTP in the paystack portal
        // $response = $this->paystackService->finalizeTransfer($transferCode);

        $user->notify(new Withdraw($user));
        // return Success
        return $this->successResponse("Withdrawal of $request->amount Complete");
    }

    public function deposit(Request $request)
    {
        $request->validate([
            "amount" => ['required', 'min:500', 'integer'],
            'payment_method' => ['required', "in:" . PaymentMethod::NEW_CARD . ',' . PaymentMethod::SAVED_CARD],
            'pin' => ['required'],

        ]);

        // get user object of auth user
        $user = User::find(auth()->id());

        $user->validatePin($request->pin);

        $type = TransactionType::CREDIT_WALLET;

        $amountInKobo = $request->amount * 100;


        switch ($request->payment_method) {
            case PaymentMethod::NEW_CARD:
                $amountInKobo = $request->amount * 100;

                $response = $this->paystackService->initializeTransaction($user->email, $amountInKobo, $this->generateReference($type), $type);
                // $response = $this->squadcoService->initiateTransaction($user->email, $amountInKobo, $this->generateReference($type), $type);

                return $this->successResponse("Payment page URL generated for wallet deposit", $response->data);
                // return $this->successResponse("Payment page URL generated for wallet deposit", [
                //     "authorization_url" => $response->data->checkout_url,
                //     "reference" => $response->data->transaction_ref
                // ]);
                break;
            case PaymentMethod::SAVED_CARD:
                if (!$user->authorizedCard) {
                    return $this->failureResponse("You do not have a saved card yet", Response::HTTP_BAD_REQUEST);
                }

                // $response = $this->squadcoService->chargeAuthorization(
                //     $amountInKobo,
                //     $user->authorizedCard->authorization_code,
                //     $this->generateReference($type)
                // );

                $response = $this->paystackService->chargeAuthorization($user->email, $amountInKobo, $user->authorizedCard->authorization_code, $this->generateReference($type));

                // if transaction fialed, return falure
                if (!$response->success) {
                    return $this->failureResponse("Deposit failed! reason: $response->message", Response::HTTP_OK);
                }

                // if transation was successful,get amount from the verification and deposit into wallet.
                $amountToDeposit = $response->data->transaction_amount / 100;
                $user->walletDeposit($amountToDeposit);

                // Award point for the wallet being funded
                $user->rewardPointFor(RewardAction::WALLET_FUNDED);

                // notify user about deposit
                $user->notify(new Deposit($user));
                // return success
                return $this->successResponse("Successfully deposited $amountToDeposit into wallet");
                break;

            default:
                return $this->failureResponse("The payment method \"$request->payment_method\" is invalid", Response::HTTP_UNPROCESSABLE_ENTITY);

                break;
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'min:1000', 'max:500000'],
            'phone_number' => ['required', 'string', 'max:15'],
        ]);

        $beneficiary = User::where('phone_number', $request->phone_number)->first();

        if (!$beneficiary || !$beneficiary->hasVerifiedEmail() || !$beneficiary->is_active) {
            return $this->failureResponse("The beneficairy account does not exist or is currently inactive", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sender = User::find(auth()->id());

        $sender->walletTransfer($beneficiary, $request->amount);

        return $this->successResponse("Transfer successful");
    }

    public function transferToMerchant(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'min:1000', 'max:500000'],
            'merchant_code' => ['required', 'string', 'max:10'],
        ]);

        $beneficiaryMerchant = MerchantDetail::where('merchant_code', strtoupper($request->merchant_code))->first();

        if (!$beneficiaryMerchant || !$beneficiaryMerchant->user->hasVerifiedEmail() || !$beneficiaryMerchant->user->is_active) {
            return $this->failureResponse("The beneficairy account does not exist or is currently inactive", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sender = User::find(auth()->id());

        $sender->walletTransfer($beneficiaryMerchant->user, $request->amount);

        return $this->successResponse("Transfer to merchant successful");
    }

    public function balance(Request $request)
    {
        $user = User::find(auth()->id());

        return $this->successResponse("Wallet balance", [
            'balance' => $user->balance
        ]);
    }
}
