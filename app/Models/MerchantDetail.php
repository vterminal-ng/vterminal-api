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
        'business_city',
        'business_address',
        'business_verified_at',
        'has_physical_location',
        'registered_business_name',
        'cac_document',
        'cac_uploaded_at',
        'rc_number',
        'date_of_registration',
        'type_of_company',
        'tin_number',
        'tin_verified_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
