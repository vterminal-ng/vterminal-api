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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
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

        if($user->userTransactionPin) {
            return $this->failureResponse(
                "Pin Created Already!",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
        Pin::create([
            'user_id' => $user->id,
            'pin' => $request->transaction_pin,
            'status' => 'active',
        ]);

        return $this->successResponse(
            "Transaction Pin Created Successfully", NULL,
            Response::HTTP_CREATED
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pin  $pin
     * @return \Illuminate\Http\Response
     */
    public function show(Pin $pin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pin  $pin
     * @return \Illuminate\Http\Response
     */
    public function edit(Pin $pin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pin  $pin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pin $pin)
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

        $userPin = $user
            ->userTransactionPin
            ->fill($request->only(
                [
                    'pin',
                ]
            ));

        if ($userPin->isClean()) return $this->failureResponse('Please enter a transaction pin', Response::HTTP_NOT_ACCEPTABLE);

        $userPin->save();

        return $this->successResponse(
            "Transaction Pin Updated", NULL
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pin  $pin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pin $pin)
    {
        //
    }
}
