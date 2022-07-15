<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizedCard extends Model
{
    use HasFactory;

    protected $fillable = [
        "authorization_code",
        "card_type",
        "last4",
        "exp_month",
        "exp_year",
        "bin",
        "bank",
        "channel",
        "signature",
        "account_name",
        "reference",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
