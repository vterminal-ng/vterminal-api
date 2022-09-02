<?php

namespace App\Http\Controllers\Paystack;

use App\Constants\CodeStatus;
use App\Constants\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Code;
use Illuminate\Http\Request;
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
        Log::info("Recieved $event->event event from paystack with $event->data->status status");
        $transaction = $event->data->metadata->transaction;

        if ($event->event  == 'charge.success') {
            switch ($transaction->type) {
                case TransactionType::VDEPOSIT:
                    if ($event->data->status == "failed") {
                        Log::info("Deposit failed. Reason: $event->data->gateway_response");

                        exit();
                    }
                    $code = Code::where('code', $request->transaction_code)->first();

                    // if transaction code status is \anything other than PENDING, then it is invalid,
                    // we can't activate code that isn't pending
                    if ($code->status != CodeStatus::PENDING) {
                        // refund payment
                        $this->paystackService->refundTransaction($request->paystack_reference);
                        return $this->failureResponse("Invalid transaction code", Response::HTTP_BAD_REQUEST);
                    }

                    // activate code
                    $code->forceFill([
                        'status' => CodeStatus::ACTIVE
                    ])->save();
            }
            // check transaction type
            // cheange status of code to active OR deposit into user wallet
        }
    }

    public function handleWebhookGateway(Request $request)
    {
        $input = @file_get_contents("php://input");
        $event = json_decode($input, true);
        if ($event['event'] == 'charge.success') {

            // normalize event data
            $data = (new NormalizeWebhookChargeSuccessPayload())->normalizeData($event);

            // If payment by payment link
            if (isset($data['referrer'])) {
                $credit_wallet = (new PaymentLinkCreditWallet())->creditWallet($data);
                info('Wallet funed via payment link: ' . $data['wallet_id']);

                // Acknowledge paystack response if wallet is credited.
                ($credit_wallet['status']) ? http_response_code(200) : '';
            }

            // if authorization_status is [true] then add card to users authorization card list.
            if (($data['authorization_status'])) {

                // Add card with card to Card Authorization Service.
                $card = (new AddCardAuthorizationService())->Add($data);

                // Add authorization to invited user GroupPlanInvitation
                if (isset($data['user_plan_id']) && isset($data['group_plan_invitation_id']) && !empty($data['group_plan_invitation_id'])) {
                    $user_group_plan = (new GroupPlanInvitationAddCardAuthorizationService())->add($data, $card);
                }

                // Charge the first recuring amount after authorization.
                // attach the card id to the users plan.
                if (isset($data['user_plan_id']) && !isset($data['group_plan_invitation_id']) && !empty($data['user_plan_id'])) {
                    $first_recuring_charge = (new ChargeFirstRecurringAmountAfterAuthorizationService())->charge($data, $card);
                }

                // Acknowledge paystack response if authorization is saved.
                ($card['status']) ? http_response_code(200) : '';
            }

            // Dedicated NUBAN credit wallet when transfer is received.
            if (($data['channel'] == 'dedicated_nuban')) {
                $credit_wallet = (new DedicatedNubanCreditWalletForTransfer())->creditWallet($data);

                // Acknowledge paystack response if wallet is credited.
                ($credit_wallet['status']) ? http_response_code(200) : '';
            }
        }

        if ($event['event'] == 'transfer.success') {
        }

        if ($event['event'] == 'customeridentification.success') {

            // Generate dedicated Nuban for user
            $resUrl = config('app.paystack_payment_url') . '/dedicated_account';
            $params = [
                'customer' => $event['data']['customer_code'],
                'preferred_bank' => 'wema-bank',
            ];
            $payload = $this->performRequest('POST', $resUrl, $params);

            $user = User::where('email', $payload['data']['customer']['email'])->first();
            $user->has_nuban = true;
            $user->save();


            UserNubanAccount::create([
                'user_id' => $user->id,
                'bank_name' => $payload['data']['bank']['name'],
                'bank_id' =>  $payload['data']['bank']['id'],
                'account_number' => $payload['data']['account_number'],
                'account_name' => $payload['data']['account_name'],
                'payload' => serialize($payload),
                'is_active' => $payload['data']['active'],
                'paystack_customer_code' => $payload['data']['customer']['customer_code'],
            ]);


            // Start Update flag to the Halo Auth UserMicroService
            $formParams = [
                'method' => 'POST', 'path' => 'user/bvn-nuban-flag/update', 'params' => [
                    'email' => $user->email,
                ],
            ];
            $flag_update = (new HaloAuthApiGatewayService())->send($formParams);
            // End Update flag to the Halo Auth UserMicroService


            // Acknowledge Paystack webhook request
            http_response_code(200);
        }

        return  "Data was returned for user";


        // Do something with $event
        http_response_code(200); // PHP 5.4 or greater
    }
}
