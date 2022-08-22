<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function dispute(Request $request, Transaction $transaction) // Get correct model name after Gabi push
    {
        dd($transaction);

        $ticketFileds = $request->validate([
            'transactiontype' => ['in:deposit,withdrawal'],
            'subject' => ['required','string'],
            'description' => ['text','max:255'],
        ]);

        SupportTicket::create([
            "transaction_id" => $transaction->id,
            "ticket_type" => "dispute",
            "transaction_type" => $ticketFileds['transactiontype'],
            "subject" => $ticketFileds['subject'],
            "description" => $ticketFileds['description'],
        ]);
    }
}
