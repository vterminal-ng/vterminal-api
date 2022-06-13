<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'other_names',
        'date_of_birth',
        'gender',
        'referral_code',
        'referrer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
