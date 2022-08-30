<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identity_type',
        'identity_number',
        'id_base64_string',
        'passport_base64_string',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'reference',
        'payload',
        'phone_number',
        'gender'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
