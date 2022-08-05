<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pin;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PinController extends Controller
{

    use ApiResponder;
    /**
     * For creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'transaction_pin' => ['string', 'size:6'],
            'status' => ['in:active,inactive'],
        ]);

        // get authenticated user instance
        $user = auth()->user();

        if ($user->userTransactionPin) {
            return $this->failureResponse(
                "Pin Created Already!",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
        $user->pin()->create([
            'pin' => $request->transaction_pin,
            'status' => 'active',
        ]);

        return $this->successResponse(
            "Transaction Pin Created Successfully",
            NULL,
            Response::HTTP_CREATED
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pin  $pin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'new_pin' => ['string', 'size:6'],
        ]);

        // get authenticated user instance
        $user = auth()->user();

        // dd($user->userDetail);
        if (!$user->userTransactionPin) {
            return $this->failureResponse(
                "No Transaction Pin Set. Please create a new one",
                Response::HTTP_NOT_FOUND
            );
        }

        $userPin = Pin::all();
        $userPin->update([
            'pin' => $request->new_pin
        ]);
        // $userPin = $user
        //     ->userTransactionPin
        //     ->fill($request->only(
        //         [
        //             'new_pin',
        //         ]
        //     ));

        // if ($userPin->isClean()) {
        //     return $this->failureResponse('Please enter a transaction pin', Response::HTTP_NOT_ACCEPTABLE);
        // }

        $userPin->save();

        return $this->successResponse(
            "Transaction Pin Updated",
            NULL
        );
    }
}
