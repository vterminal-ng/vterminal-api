<?php

namespace App\Http\Controllers\API;

use App\Constants\BankListChannel;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankDetailResource;
use App\Models\BankDetail;
use App\Models\User;
use App\Services\PaystackService;
use App\Services\NubanService;
use App\Services\VerifyMeService;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankDetailController extends Controller
{
    use ApiResponder;

    protected $paystackService;
    protected $verifyMeService;
    protected $nubanService;

    public function __construct(PaystackService $paystackService, VerifyMeService $verifyMeService, NubanService $nubanService)
    {
        $this->paystackService = $paystackService;
        $this->verifyMeService = $verifyMeService;
        $this->nubanService = $nubanService;
    }


    public function create(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'string', 'min:3'],
            'account_name' => ['required', 'min:3'],
            'bank_name' => ['required', 'string', 'min:3'],
            'bank_code' => ['required']
        ]);

        //Check if user already added bank details
        $detail = BankDetail::where('user_id', auth()->id())->first();

        if ($detail) {
            return $this->failureResponse(
                "You can only add 1 Bank Account Details",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        // Create transfer receipient
        $response = $this->paystackService->createTranferRecipient(
            $request->account_name,
            $request->account_number,
            $request->bank_code,
            metadata: [
                'user_id' => auth()->id(),
            ]
        );

        // Get recipient code from response
        $recipientcode = $response->data->recipient_code;

        //dd($recipientcode);

        $bankDetails = BankDetail::create([
            'user_id' => auth()->id(),
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'recipient_code' => $recipientcode
        ]);

        return $this->successResponse(
            "Bank Details Added Successfully",
            [new BankDetailResource($bankDetails), $response->message],
            Response::HTTP_CREATED
        );
    }

    public function getBankDetail()
    {
        $user = auth()->user();
        if (!$user->bankDetail) {
            return $this->failureResponse(
                "No Bank Details Found",
                Response::HTTP_NOT_FOUND
            );
        }
        return $this->successResponse(
            "Bank Details Found",
            new BankDetailResource($user->bankDetail)
        );
    }

    public function deleteBankDetail(User $user, BankDetail $bankDetail)
    {
        $user = auth()->user();

        $bankDetail = $user->bankDetail;

        if (!$bankDetail) {
            return $this->failureResponse(
                "No Bank Details Found",
                Response::HTTP_NOT_FOUND
            );
        }

        $rcode = $bankDetail->recipient_code;

        $this->authorize('delete', $bankDetail);

        $response = $this->paystackService->deleteTranferRecipient($rcode);

        //dd($response);

        $bankDetail->delete();

        return $this->successResponse(
            ["Bank Details Deleted", $response->message],
            NULL,
            Response::HTTP_NO_CONTENT
        );
    }

    public function getBanks()
    {
        $codes = null;
        switch (config('services.bank_list.channel')) {
            case BankListChannel::NUBAN_API:
                $codes = $this->nubanService->getBanks();
                break;
            case BankListChannel::VERIFY_ME:
                $codes = $this->verifyMeService->getBanks();
                break;
            case BankListChannel::PAYSTACK:
                $codes = $this->paystackService->listBanks();
                break;
            default:
                return $this->failureResponse('Bank list channel not available. Contact Admin.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->successResponse("List of Banks and their codes", $codes->data ?? null);
    }

    public function getAccountDetails(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'account_no' => ['required', 'string', 'size:10'],
            'bank_code' => ['required', 'string'],
        ]);

        switch (config('services.bank_list.channel')) {
            case BankListChannel::NUBAN_API:
                $accountDetails = $this->nubanService->getAccountDetails($request->account_no, $request->bank_code);

                // "bank_name": "FIDELITY BANK",
                // "account_name": "GABRIEL TOCHUKWU IBENYE",
                // "account_number": "6080266119",
                // "bank_code": "070",
                // "requests": "unlimited",
                // "execution_time": "0.66s"
                break;
            case BankListChannel::VERIFY_ME:
                $accountDetails = $this->verifyMeService->getAccountDetails($request->account_no, $request->bank_code);
                $accountDetails = $accountDetails->data;
                // "accountName": "JOHN DOE",
                // "accountNumber": "1000000001",
                // "lastname": "DOE",
                // "firstname": "JOHN",
                // "middlename": "",
                // "accountCurrency": "NGN",
                // "dob": "1999-09-10",
                // "mobileNumber": "08000000000",
                // "bvn": "10000000001"
                break;
            case BankListChannel::PAYSTACK:
                $accountDetails = $this->paystackService->resolveAccountNumber($request->account_no, $request->bank_code);
                $accountDetails = $accountDetails->data;
                // "account_name": "GABRIEL TOCHUKWU IBENYE",
                // "account_number": "6080266119"
                break;
            default:
                return $this->failureResponse('Bank list channel not available. Contact Admin.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        // dd($accountDetails);
        return $this->successResponse("Account Details", $this->normalizeAccountDetails($accountDetails));
    }

    protected function normalizeAccountDetails($accountDetails)
    {
        return [
            "accountName" => $accountDetails->accountName ?? $accountDetails->account_name,
            "accountNumber" => $accountDetails->accountNumber ?? $accountDetails->account_number,
        ];
    }
}
