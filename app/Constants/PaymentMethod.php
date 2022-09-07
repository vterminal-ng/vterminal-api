<?php

namespace App\Constants;

class PaymentMethod
{
    public const WALLET = "wallet";
    public const SAVED_CARD = "saved_card";
    public const NEW_CARD = "new_card";

    public static function all($separator = ',')
    {
        return SELF::WALLET . $separator . SELF::SAVED_CARD . $separator . SELF::NEW_CARD;
    }
}
