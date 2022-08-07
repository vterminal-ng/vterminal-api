<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pin;
use App\Rules\CheckCurrentAndNewPin;
use App\Rules\CheckCurrentPin;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

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
            'pin' => ['required', 'string', 'size:4', 'confirmed'],
        ]);

        // get authenticated user instance
        $user = auth()->user();

        if ($user->pin) {
            return $this->failureResponse(
                "Pin already created! Try updating pin",
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
        $user->pin()->create([
            'pin' => Hash::make($request->pin),
            'status' => 'active',
        ]);

        return $this->successResponse(
            "Transaction Pin Created Successfully",
            code: Response::HTTP_CREATED
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
            'current_pin' => ['required', new CheckCurrentPin()],
            'new_pin' => ['required', 'size:4', new CheckCurrentAndNewPin(), 'confirmed'],
        ]);

        $user = auth()->user();

        $user->pin()->update([
            'pin' => Hash::make($request->new_pin)
        ]);

        return $this->successResponse("Transaction Pin Updated");
    }
}
