<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankDetailResource;
use App\Models\BankDetail;
use App\Models\User;
use App\Services\PaystackService;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankDetailController extends Controller
{
    use ApiResponder;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'string', 'min:3'],
            'account_name' => ['required', 'min:3'],
            'bank_name' => ['required', 'string', 'min:3'],
            'bank_code' => ['required']
        ]);

        //Check if user already added the same bank details
        $detail = BankDetail::where('user_id', auth()->id())->first();

        if ($detail) {
            return $this->failureResponse(
                "You can only add 1 Bank Account Details",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        $bankDetails = BankDetail::create([
            'user_id' => auth()->id(),
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code
        ]);

        // Create transfer receipt
        $response = $this->paystackService($request->account_name, $request->account_number, $request->bank_code);
        
        dd($response['data']->recipient_code);
        
        return $this->successResponse(
            "Bank Details Added Successfully",
            [new BankDetailResource($bankDetails), $response->message],
            Response::HTTP_CREATED
        );
    }

    public function getBankDetail() {
        $user = auth()->user();
        if(!$user->bankDetail) {
            return $this->failureResponse(
                "No Bank Details Found",
                Response::HTTP_NOT_FOUND
            );
        }
        return $this->successResponse(
            "Bank Details Found",
            [
                "bank_details" => new BankDetailResource($user->bankDetails)
            ],
            Response::HTTP_FOUND
        );
    }

    public function updateBankDetail(Request $request, BankDetail $bankDetail)
    {
        
        $request->validate([
            'account_number' => ['required', 'string', 'min:3'],
            'account_name' => ['required', 'min:3'],
            'bank_name' => ['required', 'string', 'min:3'],
        ]);


        $this->authorize('update', $bankDetail);

        //Check if user already added the same bank details
        $detail = BankDetail::where('user_id', auth()->id())
            ->where('account_number', '=', $request->account_number)
            ->where('bank_name', '=', $request->bank_name)
            ->first();

        if ($detail) {
            return $this->failureResponse(
                "You added this Bank Detail already",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        $bankDetail->update(
                [
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                    'bank_name' => $request->bank_name,
                ]
            );

        $bankDetail->save();

        return $this->successResponse(
            "Bank Detail Updated",
            [
                'bankDetail' => new BankDetailResource($bankDetail)
            ]
        );
    }

    public function deleteBankDetail(BankDetail $bankDetail)
    {

        $this->authorize('delete', $bankDetail);

        $bankDetail->delete();

        return $this->successResponse(
            "Bank Details Deleted",
            NULL,
            Response::HTTP_NO_CONTENT
        );
    }


}
