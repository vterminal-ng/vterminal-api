<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'merchant_id',
        'code',
        'transaction_type',
        'status',
        'amount',
        'charge_amount',
        'charge_on_card',
        'vterminal_charge',
        'merchant_charge',
    ];


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }
}
