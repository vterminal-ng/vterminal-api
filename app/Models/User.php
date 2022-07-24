<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements Wallet
{
    use HasApiTokens, HasFactory, Notifiable, HasWallet;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'phone_number',
        'phone_number_verified_at',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function merchantDetail()
    {
        return $this->hasOne(MerchantDetail::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function authorizedCard()
    {
        return $this->hasOne(AuthorizedCard::class);
    }

    public function role()
    {
        return $this->role;
    }

    public function hasVerifiedPhone()
    {
        return (bool)$this->phone_number_verified_at;
    }

    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_number_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    public function isMerchant()
    {
        return $this->role() === 'merchant';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function isProfileVerified()
    {
        return (bool)($this->hasVerifiedEmail() && $this->hasVerifiedPhone() && $this->userDetail && $this->bankDetail);
    }
}
