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

    protected $appends = ['full_name', 'initials'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return ($this->first_name . ' ' . $this->last_name) ?? 'No Name';
    }

    public function getInitialsAttribute()
    {
        return ($this->first_name[0] . $this->last_name[0]) ?? 'VT';
    }
}
