<?php

namespace App\Traits;

use App\Exceptions\InvalidTransactionPin;
use Illuminate\Support\Facades\Hash;

trait HasPin
{
    public function hasSetPin(): bool
    {
        return !is_null($this->pin);
    }

    public function validatePin($pin)
    {
        if (!Hash::check($pin, $this->pin->pin)) {
            throw new InvalidTransactionPin;
        }
    }
}
