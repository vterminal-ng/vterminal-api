<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;


class PaystackService
{
    use ConsumeExternalService, ApiResponder;

    protected $baseUri;
    protected $secret;

    public function __construct()
    {
        $this->baseUri = config('services.paystack.base_url');
        $this->secret = config('services.paystack.secret');
    }

    public function verifyTransaction($reference)
    {
        $response = $this->performRequest('GET', "/transaction/verify/$reference");
        // dd($response);

        return json_decode((string)$response);
    }

    public function checkAuthorization($email, $amount, $authorizationCode)
    {
        $response = $this->performRequest(
            'POST',
            "/transaction/check_authorization",
            [
                'email' => $email,
                'amount' => $amount,
                'authorization_code' => $authorizationCode,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function chargeAuthorization($email, $amount, $authorizationCode, $reference)
    {
        $response = $this->performRequest(
            'POST',
            "/transaction/charge_authorization",
            [
                'email' => $email,
                'amount' => $amount,
                'authorization_code' => $authorizationCode,
                'reference' => $reference,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function createTranferRecipient($name, $accountNumber, $bankCode)
    {
        $response = $this->performRequest(
            'POST',
            "/transferrecipient",
            [
                "type" => "nuban",
                "name" => $name,
                "account_number" => $accountNumber,
                "bank_code" => $bankCode,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function initiateTransfer($amount, $recipientCode)
    {
        $response = $this->performRequest(
            'POST',
            "/transfer",
            [
                "source" => "nuban",
                "amount" => $amount,
                "recipient" => $recipientCode,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function finalizeTransfer($transferCode)
    {
        $response = $this->performRequest(
            'POST',
            "/transfer/finalize_transfer",
            [
                "transfer_code" => $transferCode,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function createCustomer($email, $firstName, $lastName, $phone, $metadata = [])
    {
        $response = $this->performRequest(
            'POST',
            "/customer",
            [
                "email" => $email,
                "first_name" => $firstName,
                "last_name" => $lastName,
                "phone" => $phone,
                "metadata" => $metadata,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function createDedicatedVirtualAccount($customerCode)
    {
        $response = $this->performRequest(
            'POST',
            "/dedicated_account",
            [
                "customer" => $customerCode,
            ],
            [
                "Content-Type" => "application/json",
            ]
        );
        // dd($response);

        return json_decode((string)$response);
    }
}
