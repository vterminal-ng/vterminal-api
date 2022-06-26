<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankDetailResource;
use App\Models\BankDetail;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankDetailController extends Controller
{
    use ApiResponder;

    public function create(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'string', 'min:3'],
            'account_name' => ['required', 'min:3'],
            'bank_name' => ['required', 'string', 'min:3'],
            'bank_code' => ['required']
        ]);

        //Check if user already added the same bank details
        $detail = BankDetail::where('user_id', auth()->id())
            ->where('account_number', '=', $request->account_number)
            ->where('bank_name', '=', $request->bank_name)
            ->first();

        if ($detail) {
            return $this->failureResponse(
                "Duplicate Bank Details",
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

        return $this->successResponse(
            "Bank Details Added Successfully",
            new BankDetailResource($bankDetails),
            Response::HTTP_CREATED
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
