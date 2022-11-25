<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'service_id',
        'service_name',
        'variation_code',
        'variation_name',
        'billers_code',
        'purchase_code',
        'request_id',
        'transaction_id',
        'amount',
    ];
}
