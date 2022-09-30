<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiResponder;
use App\Traits\ConsumeExternalService;
use Illuminate\Validation\Rules\In;

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

    public function initializeTransaction($email, $amountInKobo, $reference, $transactionType, $metadata = [])
    {
        $metadata['transaction_type'] = $transactionType;

        $response = $this->performRequest(
            'POST',
            "/transaction/initialize",
            [
                "email" => $email,
                "amount" => $amountInKobo,
                "reference" => $reference,
                "metadata" => $metadata
            ]
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

    public function refundTransaction($reference)
    {
        $response = $this->makeRequest(
            'POST',
            "/refund",
            [
                "transaction" => $reference
            ]
        );
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
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function createTranferRecipient($name, $accountNumber, $bankCode, $metadata = [])
    {
        $response = $this->performRequest(
            'POST',
            "/transferrecipient",
            [
                "type" => "nuban",
                "name" => $name,
                "account_number" => $accountNumber,
                "bank_code" => $bankCode,
                "metadata" => $metadata
            ]
        );
        //dd($response);

        return json_decode((string)$response);
    }

    public function fetchTranferRecipient($transferRecipientCode)
    {
        $response = $this->makeRequest(
            'GET',
            "/transferrecipient/$transferRecipientCode",
        );
        //dd($response);

        return json_decode((string)$response);
    }

    public function deleteTranferRecipient($recipientCode)
    {
        $response = $this->performRequest(
            'DELETE',
            "/transferrecipient/$recipientCode",
        );
        //dd($response);

        return json_decode((string)$response);
    }

    public function initiateTransfer($amount, $recipientCode)
    {
        $response = $this->makeRequest(
            'POST',
            "/transfer",
            [
                "source" => "balance",
                "amount" => $amount,
                "recipient" => $recipientCode,
            ],

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
        );


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
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function listBanks()
    {
        $response = $this->performRequest(
            'GET',
            "/bank",
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function resolveAccountNumber($accountNumber, $bankCode)
    {
        $response = $this->performRequest(
            'GET',
            "/bank/resolve?account_number=$accountNumber&bank_code=$bankCode"
        );
        // dd($response);

        return json_decode((string)$response);
    }

    public function calculateApplicableFee(int $amount): int
    {
        $decimalFee = 1.5 / 100;
        $flatFee = 100;
        $feeCap = 2000;

        $applicableFee = round((($decimalFee * $amount) + $flatFee), 2);

        if ($amount < 2500) {
            $applicableFee = round(($decimalFee * $amount), 2);
        }

        if ($applicableFee > $feeCap) {
            $applicableFee = $feeCap;
        }

        return $applicableFee;
    }
}
