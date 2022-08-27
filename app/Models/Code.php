<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Code extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'merchant_id',
        'code',
        'transaction_type',
        'status',
        'subtotal_amount',
        'total_amount',
        'charge_amount',
        'charge_from',
        'reference',
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

    public function isCodeExpired()
    {
        $now = Carbon::now()->timestamp;

        $timeDifference = $now - strtotime($this->created_at);

        $minutes = round($timeDifference / 60);

        return $minutes > 60;
    }
}
