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
        'address_confirmation',
        'address_verified_at',
        'upload_successfull',
        'disk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImagesAttribute()
    {
        return [
            "original" => $this->getImagePath("original"),
        ];
    }

    public function getImagePath($size)
    {
        return Storage::disk($this->disk)->url("uploads/original/{$size}/" . $this->image);
    }
}
