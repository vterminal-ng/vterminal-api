<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;

trait HasPin
{
    public function hasSetPin(): bool
    {
        return !is_null($this->pin);
    }

    public function validatePin($pin): bool
    {
        return Hash::check($pin, $this->pin->pin);
    }
}
