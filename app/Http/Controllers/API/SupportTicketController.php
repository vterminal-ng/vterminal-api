<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupportTicketController extends Controller
{
    use ApiResponder;

    public function createTicket(Request $request) // Transaction $transaction // TODO: Get correct transaction model
    {
        //dd($transaction);

        $ticketFileds = $request->validate([
            'tickettype' => ['required','in:general,dispute'],
            'transactiontype' => ['in:deposit,withdrawal'],
            'subject' => ['required','string'],
            'description' => ['string','max:255'],
        ]);

        if($request->tickettype === 'dispute') {
            $transreference = "123456er78"; //TODO: $transaction->reference;

            $ticket = SupportTicket::create([ // Transactoin reference 
                'ticket_type' => $ticketFileds['tickettype'],
                'transaction_type' => $ticketFileds['transactiontype'],
                'transaction_reference' => $transreference, //$transaction->reference;
                'subject' => $ticketFileds['subject'],
                'description' => $ticketFileds['description'] ?? NULL,
            ]);
        } else {

            $ticket = SupportTicket::create([ // Transactoin reference 
                'ticket_type' => $ticketFileds['tickettype'],
                'subject' => $ticketFileds['subject'],
                'description' => $ticketFileds['description'] ?? NULL,
            ]);
        }

        return $this->successResponse(
            "Ticket Created Successfully",
            [
                "ticket" => $ticket
            ],
            code: Response::HTTP_CREATED
        );
    }
}
