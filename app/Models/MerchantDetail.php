<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_state',
        'business_address',
        'business_verified_at',
        'has_physical_location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
