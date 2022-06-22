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

    public function create(Request $request) {
        $request->validate([
            'user_id' => ['required', 'integer'],
            'account_number' => ['required', 'string', 'min:3'],
            'account_name' => ['required', 'min:3'],
            'bank_name' => ['required', 'string', 'min:3'],
            'bank_code' => ['required']
        ]);

        $user = User::findOrFail($request->user_id);

        if(!(auth()->id() === $user->id)) {
            return $this->failureResponse(
                "You are not authorized to access this resource",
                Response::HTTP_UNAUTHORIZED
            );
        }

        //Check if user already added the same bank details
        $detail = BankDetail::where('user_id', $request->user_id)
        ->where('account_number', '=', $request->account_number)
        ->where('bank_name', '=', $request->bank_name)
        ->first();

        if($detail) {
            return $this->failureResponse(
                "Duplicate Bank Details",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        $bankDetails = BankDetail::create([
            'user_id' => $request->user_id,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code
        ]);

        return $this->successResponse(
            "Bank Details Added Successfully",            [
                "bankDetail" => new BankDetailResource($bankDetails)
            ],
            Response::HTTP_CREATED
        );
    }

    public function delete(Request $request) {
        $request->validate([
            'user_id' => ['required', 'integer'],
            'account_number' => ['required'],
            'bank_name' => ['required']
        ]);

        $user = User::findOrFail($request->user_id);

        if(!(auth()->id() === $user->id)) {
            return $this->failureResponse(
                "You are not authorized to access this resource",
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Get bank details
        $detail = BankDetail::where('user_id', $request->user_id)
        ->where('account_number', '=', $request->account_number)
        ->where('bank_name', '=', $request->bank_name)
        ->first();

        if(!$detail) {
            return $this->failureResponse(
                "Bank Details Not Found",
                Response::HTTP_NOT_FOUND
            );
        }
        //dd($detail);
        $detail->delete();

        return $this->successResponse(
            "Bank Details Deleted", NULL,
            Response::HTTP_OK
        );
    }
}
