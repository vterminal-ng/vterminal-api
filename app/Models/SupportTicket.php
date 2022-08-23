<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ticket_type',
        'transaction_reference',
        'transaction_type',
        'subject',
        'description',
        'status'
    ];
}
