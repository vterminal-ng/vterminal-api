<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Traits\ApiResponder;
use App\Traits\Generators;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    use ApiResponder, Generators;

    public function transactionSummary(Request $request)
    {
        // validate amount and is_card_charged

        // calculate charge
        // subtotal
        // total
        // transaction type
        // is card charged

        //return

    }

    public function generateCode(Request $request)
    {
        $request->validate([
            "amount" => ['required', 'integer'],
            "type" => ['required', 'in:withdrawal,deposit'],
            "charge_on_card" => ['required', 'boolean'],
        ]);

        // caculate charge...possible endpoint?
        $charge = null;

        // gen code
        $transCode = $this->generateTransCode();

        //save code

        Code::create([
            'customer_id' => auth()->id(),
            'code' => $transCode,
            'type' => $request->type,
            'amount' => $request->amount,
            'charge_amount' => $charge,
            'charge_on_card' => $request->charge_on_card
        ]);

        return $this->successResponse("Generated code successfully");
    }
}
