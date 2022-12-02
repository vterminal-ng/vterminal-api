<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizedCard extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "authorization_code",
        "card_type",
        "card_pan",
        "last4",
        "exp_month",
        "exp_year",
        "bin",
        "bank",
        "signature",
        "account_name",
        "reference",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
