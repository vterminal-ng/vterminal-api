<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'bank_name',
        'bank_code',
        'is_verified',
        'recipient_code',
        'paystack_customer_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
