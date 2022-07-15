<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PaystackService;


class AuthorizedCardController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function add(Request $request, User $user)
    {
        $request->validate([
            'reference' => ['required', 'string']
        ]);

        // validate transaction ref

        // save card details

        // refund transaction

        // return success
    }

    public function delete(User $user)
    {
        // code ...
    }
}
