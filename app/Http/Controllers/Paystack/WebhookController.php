<?php

namespace App\Http\Controllers\Paystack;

use App\Constants\CodeStatus;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CodeResource;
use App\Http\Resources\UserResource;
use App\Models\Code;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function webhook(Request $request)
    {
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER)) {
            // only a post with paystack signature header gets our attention
            exit();
        }
        // Retrieve the request's body
        $input = @file_get_contents('php://input');
        define('PAYSTACK_SECRET_KEY', config('services.paystack.secret'));
        if (!$_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] || ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))) {
            // silently forget this ever happened
            exit();
        }
        http_response_code(200);
        // parse event (which is json string) as object
        // Do something — that will not take long — with $event
        $event = json_decode($input);
        Log::info("Recieved $event->event event from paystack with {$event->data->status} status");
        /**
         * Expected fields in metadata
         * transaction_ => [
         *  type 
         *  code
         * ]
         */
        $transactionType = $event->data->metadata->transaction_type ?? null;
        $transactionCode = $event->data->metadata->transaction_code ?? null;

        if ($event->event  == 'charge.success') {
            if (is_null($transactionType)) {
                Log::info("Transaction type is null");
                exit();
            }
            switch ($transactionType) {
                case TransactionType::VWITHDRAWAL:
                    Log::info("");
                    Log::info("===================================================");
                    Log::info("ACTIVATING VTERMINAL WITHDRAWAL TRANSACTION CODE");
                    Log::info("===================================================");
                    Log::info("");

                    Log::info("Checking status of the paystack transaction. The status is \"{$event->data->status}\"");
                    if ($event->data->status == "failed") {
                        Log::info(" Code activation payment failed. Reason: {$event->data->gateway_response}",);
                        exit();
                    }

                    Log::info("Finding transaction code $transactionCode in database.");
                    $code = Code::with(['customer'])->where('code', $transactionCode)->first();

                    Log::info("Checking if code status is valid for activation");
                    // if transaction code status is \anything other than PENDING, then it is invalid,
                    // we can't activate code that isn't pending
                    if (!$code || $code->status != CodeStatus::PENDING) {
                        // refund payment
                        $this->paystackService->refundTransaction($event->data->reference);
                        // $code->customer->deposit($code->total_amount);
                        Log::error("Invalid transaction code. Reason: Code status is not pending");
                        exit();
                    }
                    Log::info("Found the code $transactionCode");
                    Log::info("The Code details ", ["code" => new CodeResource($code)]);
                    Log::info("Code status is valid for activation");

                    Log::info("Activating Code");
                    // activate code
                    $code->forceFill([
                        'status' => CodeStatus::ACTIVE
                    ])->save();
                    Log::info("Code activated successfully");

                    break;
                case TransactionType::CREDIT_WALLET:
                    Log::info("");
                    Log::info("===================================================");
                    Log::info("CREDITING VTERMINAL USER WALLET");
                    Log::info("===================================================");
                    Log::info("");
                    Log::info("Checking status of the paystack transaction. The status is \"{$event->data->status}\"");
                    // if transaction fialed, return falure
                    if ($event->data->status == "failed") {
                        Log::info("Wallet deposit payment failed. Reason: {$event->data->gateway_response}");
                        exit();
                    }
                    // get user object of auth user
                    Log::info("Finding user with email: {$event->data->customer->email}");
                    $user = User::where('email', $event->data->customer->email)->first();

                    if (!$user) {
                        Log::error("Could not find user with email: {$event->data->customer->email}");
                        exit();
                    }
                    Log::info("Found the user with email: {$event->data->customer->email}");
                    Log::info("The User ", ["user" => new UserResource($user)]);

                    Log::info("Converting the paystack amount \"{$event->data->amount}\" from kobo to naira");
                    // if transation was successful,get amount from the verification and deposit into wallet.
                    $amountToDeposit = $event->data->amount / 100;

                    Log::info("Crediting the user's wallet with $amountToDeposit");
                    Log::info("User's previous wallet balance: $user->balance");
                    $user->deposit($amountToDeposit);
                    Log::info("Done Crediting user's wallet!");
                    Log::info("User's wallet balance after crediting: $user->balance");


                    break;
                default:
                    Log::error("Error: $transactionType is not a valid transaction type");
                    exit();
            }
            // check transaction type
            // cheange status of code to active OR deposit into user wallet
        }
    }

    // public function handleWebhookGateway(Request $request)
    // {
    //     $input = @file_get_contents("php://input");
    //     $event = json_decode($input, true);
    //     if ($event['event'] == 'charge.success') {

    //         // normalize event data
    //         $data = (new NormalizeWebhookChargeSuccessPayload())->normalizeData($event);

    //         // If payment by payment link
    //         if (isset($data['referrer'])) {
    //             $credit_wallet = (new PaymentLinkCreditWallet())->creditWallet($data);
    //             info('Wallet funed via payment link: ' . $data['wallet_id']);

    //             // Acknowledge paystack response if wallet is credited.
    //             ($credit_wallet['status']) ? http_response_code(200) : '';
    //         }

    //         // if authorization_status is [true] then add card to users authorization card list.
    //         if (($data['authorization_status'])) {

    //             // Add card with card to Card Authorization Service.
    //             $card = (new AddCardAuthorizationService())->Add($data);

    //             // Add authorization to invited user GroupPlanInvitation
    //             if (isset($data['user_plan_id']) && isset($data['group_plan_invitation_id']) && !empty($data['group_plan_invitation_id'])) {
    //                 $user_group_plan = (new GroupPlanInvitationAddCardAuthorizationService())->add($data, $card);
    //             }

    //             // Charge the first recuring amount after authorization.
    //             // attach the card id to the users plan.
    //             if (isset($data['user_plan_id']) && !isset($data['group_plan_invitation_id']) && !empty($data['user_plan_id'])) {
    //                 $first_recuring_charge = (new ChargeFirstRecurringAmountAfterAuthorizationService())->charge($data, $card);
    //             }

    //             // Acknowledge paystack response if authorization is saved.
    //             ($card['status']) ? http_response_code(200) : '';
    //         }

    //         // Dedicated NUBAN credit wallet when transfer is received.
    //         if (($data['channel'] == 'dedicated_nuban')) {
    //             $credit_wallet = (new DedicatedNubanCreditWalletForTransfer())->creditWallet($data);

    //             // Acknowledge paystack response if wallet is credited.
    //             ($credit_wallet['status']) ? http_response_code(200) : '';
    //         }
    //     }

    //     if ($event['event'] == 'transfer.success') {
    //     }

    //     if ($event['event'] == 'customeridentification.success') {

    //         // Generate dedicated Nuban for user
    //         $resUrl = config('app.paystack_payment_url') . '/dedicated_account';
    //         $params = [
    //             'customer' => $event['data']['customer_code'],
    //             'preferred_bank' => 'wema-bank',
    //         ];
    //         $payload = $this->performRequest('POST', $resUrl, $params);

    //         $user = User::where('email', $payload['data']['customer']['email'])->first();
    //         $user->has_nuban = true;
    //         $user->save();


    //         UserNubanAccount::create([
    //             'user_id' => $user->id,
    //             'bank_name' => $payload['data']['bank']['name'],
    //             'bank_id' =>  $payload['data']['bank']['id'],
    //             'account_number' => $payload['data']['account_number'],
    //             'account_name' => $payload['data']['account_name'],
    //             'payload' => serialize($payload),
    //             'is_active' => $payload['data']['active'],
    //             'paystack_customer_code' => $payload['data']['customer']['customer_code'],
    //         ]);


    //         // Start Update flag to the Halo Auth UserMicroService
    //         $formParams = [
    //             'method' => 'POST', 'path' => 'user/bvn-nuban-flag/update', 'params' => [
    //                 'email' => $user->email,
    //             ],
    //         ];
    //         $flag_update = (new HaloAuthApiGatewayService())->send($formParams);
    //         // End Update flag to the Halo Auth UserMicroService


    //         // Acknowledge Paystack webhook request
    //         http_response_code(200);
    //     }

    //     return  "Data was returned for user";


    //     // Do something with $event
    //     http_response_code(200); // PHP 5.4 or greater
    // }
}
