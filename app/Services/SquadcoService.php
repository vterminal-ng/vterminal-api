<?php

namespace App\Services;

use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;

class SquadcoService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $secret;

    public function __construct()
    {
        $this->baseUri = config('services.squadco.base_url');
        $this->secret = config('services.squadco.secret');
    }

    public function initiateTransaction($email, $amountInKobo, $reference, $transactionType, $metadata = [])
    {
        $metadata['transaction_type'] = $transactionType;

        $response = $this->performRequest(
            'POST',
            "/transaction/initiate",
            [
                "amount" => $amountInKobo,
                "email" => $email,
                "currency" => "NGN",
                "initiate_type" => "inline",
                "transaction_ref" => $reference,
                "payment_channels" => ["card"],
                "pass_charge" => true,
                "metadata" => $metadata,
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function chargeAuthorization($amount, $authorizationCode, $reference)
    {
        $response = $this->performRequest(
            'POST',
            "/transaction/charge_card",
            [
                "amount" => $amount,
                'token_id' => $authorizationCode,
                'transaction_ref' => $reference,
            ],
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function verifyTransaction($reference)
    {
        $response = $this->performRequest('GET', "/transaction/verify/$reference");
        // dd($response);

        return json_decode((string)$response);
    }

    public function refundTransaction($gateway_ref, $reference, $refundType = "Full", $refundAmount = null, $reason = "Refunding first payment on card made to save card")
    {
        //refund type can be "Full" or "Partial"
        $response = $this->makeRequest(
            'POST',
            "/transaction/refund",
            [
                "gateway_transaction_ref" => $gateway_ref,
                "refund_type" => $refundType,
                "reason_for_refund" => $reason,
                "transaction_ref" => $reference,
                "refund_amount" => $refundAmount
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }
}
