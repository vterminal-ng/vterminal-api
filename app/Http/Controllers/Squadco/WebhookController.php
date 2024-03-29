<?php

namespace App\Http\Controllers\Squadco;

use App\Constants\CodeStatus;
use App\Constants\RewardAction;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CodeResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WalletTransactionResource;
use App\Models\Code;
use App\Models\User;
use App\Notifications\CodeActivated;
use App\Notifications\Deposit;
use App\Services\SquadcoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $squadcoService;

    public function __construct(SquadcoService $squadcoService)
    {
        $this->squadcoService = $squadcoService;
    }
    public function webhook(Request $request)
    {
        Log::info("Recieving webhook notifcation from squadco");
        Log::info($request);
        // currently there is no token_id provided in the webhook body
        exit();
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('HTTP_X_SQUAD_ENCRYPTED_BODY', $_SERVER))
            exit();
        // Retrieve the request's body
        $input = @file_get_contents("php://input");
        define('SQUAD_SECRET_KEY',  config('services.squadco.secret')); //ENTER YOUR SECRET KEY HERE
        // validate event do all at once to avoid timing attack
        if ($_SERVER['HTTP_X_SQUAD_ENCRYPTED_BODY'] !== strtoupper(hash_hmac('sha512', $input, SQUAD_SECRET_KEY)))
            // The Webhook request is not from SQUAD 
            exit();
        http_response_code(200);
        // The Webhook request is from SQUAD
        $event = json_decode($input);

        Log::info("Recieved $event->Event event from squadco with {$event->Body->transaction_status} status");
        /**
         * Expected fields in metadata
         * transaction_ => [
         *  type 
         *  code
         * ]
         */
        $transactionType = $event->Body->meta->transaction_type ?? null;

        if ($event->Event  == 'charge_successful') {
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

                    Log::info("Checking status of the squadco transaction. The status is \"{$event->Body->transaction_status}\"");
                    if ($event->Body->transaction_status != "success") {
                        Log::info(" Code activation payment failed from squadco (ref: {$event->Body->transaction_ref})",);
                        exit();
                    }

                    Log::info("Finding transaction code with reference {$event->Body->transaction_ref} in database.");
                    $code = Code::with(['customer'])->where('reference', $event->Body->transaction_ref)->first();

                    Log::info("Checking if code status is valid for activation");
                    // if transaction code status is \anything other than PENDING, then it is invalid,
                    // we can't activate code that isn't pending
                    if (!$code || $code->status != CodeStatus::PENDING) {
                        // refund payment
                        $this->squadcoService->refundTransaction($event->Body->gateway_ref, $event->Body->transaction_ref);
                        // $code->customer->deposit($code->total_amount);
                        Log::error("Invalid transaction code. Reason: Code status is not pending");
                        exit();
                    }
                    Log::info("Found the code $code->code");
                    Log::info("The Code details ", ["code" => new CodeResource($code)]);
                    Log::info("Code status is valid for activation");

                    Log::info("Activating Code");
                    // activate code
                    $code->forceFill([
                        'status' => CodeStatus::ACTIVE
                    ])->save();

                    $code->customer->notify(new CodeActivated($code));

                    Log::info("Code activated successfully");

                    break;
                case TransactionType::CREDIT_WALLET:
                    Log::info("");
                    Log::info("===================================================");
                    Log::info("CREDITING VTERMINAL USER WALLET");
                    Log::info("===================================================");
                    Log::info("");
                    Log::info("Checking status of the squadco transaction. The status is \"{$event->Body->transaction_status}\"");
                    // if transaction fialed, return falure
                    if ($event->Body->transaction_status != "success") {
                        Log::info(" Code activation payment failed from squadco (ref: {$event->Body->transaction_ref})",);
                        exit();
                    }
                    // get user object of auth user
                    Log::info("Finding user with email: {$event->Body->email}");
                    $user = User::where('email', $event->Body->email)->first();

                    if (!$user) {
                        Log::error("Could not find user with email: {$event->Body->email}");
                        exit();
                    }
                    Log::info("Found the user with email: {$event->Body->email}");
                    Log::info("The User ", ["user" => new UserResource($user)]);

                    Log::info("Squadco transaction total amount in kobo \"{$event->Body->amount}\"");
                    Log::info("Squadco transaction amount in kobo \"{$event->Body->amount}\"");
                    // if transation was successful,get amount from the verification and deposit into wallet.
                    $amountToDeposit = $event->Body->merchant_amount;

                    Log::info("Crediting the user's wallet with $amountToDeposit");
                    Log::info("User's previous wallet balance: $user->balance");
                    $wallet = $user->walletDeposit($amountToDeposit);
                    Log::info("Wallet deposit details", ['wallet' => new WalletTransactionResource($wallet)]);
                    Log::info("Done Crediting user's wallet!");
                    Log::info("User's wallet balance after crediting: $user->balance");

                    // Award point for the wallet being funded
                    $user->rewardPointFor(RewardAction::WALLET_FUNDED);

                    // notify user about deposit
                    $user->notify(new Deposit($user));

                    break;
                case TransactionType::ADD_CARD:
                    Log::info("");
                    Log::info("===================================================");
                    Log::info("SAVING CARD DETAILS");
                    Log::info("===================================================");
                    Log::info("");

                    Log::info("Finding user with email: {$event->Body->email}");
                    $user = User::where('email', $event->Body->email)->first();
                    if (!$user) {
                        Log::error("Could not find user with email: {$event->Body->email}");
                        exit();
                    }
                    Log::info("Found the user with email: {$event->Body->email}");

                    Log::info("Saving authorization object to the database");
                    $cardPan = $event->Body->payment_information->pan;
                    $user->authorizedCard()->create([
                        "authorization_code" => $event->Body->payment_information->token_id,
                        "card_type" => $event->Body->payment_information->card_type,
                        "card_pan" => $cardPan,
                        "bin" => substr($cardPan, 0, 6),
                        "last4" => substr($cardPan, -4),
                        "reference" => $event->Body->transaction_ref,
                    ]);
                    Log::info("Saving authorization object complete");

                    Log::info("Start processing refund");
                    $this->squadcoService->refundTransaction($event->Body->gateway_ref, $event->Body->transaction_ref);

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

    //             // Acknowledge squadco response if wallet is credited.
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

    //             // Acknowledge squadco response if authorization is saved.
    //             ($card['status']) ? http_response_code(200) : '';
    //         }

    //         // Dedicated NUBAN credit wallet when transfer is received.
    //         if (($data['channel'] == 'dedicated_nuban')) {
    //             $credit_wallet = (new DedicatedNubanCreditWalletForTransfer())->creditWallet($data);

    //             // Acknowledge squadco response if wallet is credited.
    //             ($credit_wallet['status']) ? http_response_code(200) : '';
    //         }
    //     }

    //     if ($event['event'] == 'transfer.success') {
    //     }

    //     if ($event['event'] == 'customeridentification.success') {

    //         // Generate dedicated Nuban for user
    //         $resUrl = config('app.squadco_payment_url') . '/dedicated_account';
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
    //             'squadco_customer_code' => $payload['data']['customer']['customer_code'],
    //         ]);


    //         // Start Update flag to the Halo Auth UserMicroService
    //         $formParams = [
    //             'method' => 'POST', 'path' => 'user/bvn-nuban-flag/update', 'params' => [
    //                 'email' => $user->email,
    //             ],
    //         ];
    //         $flag_update = (new HaloAuthApiGatewayService())->send($formParams);
    //         // End Update flag to the Halo Auth UserMicroService


    //         // Acknowledge squadco webhook request
    //         http_response_code(200);
    //     }

    //     return  "Data was returned for user";


    //     // Do something with $event
    //     http_response_code(200); // PHP 5.4 or greater
    // }
}
