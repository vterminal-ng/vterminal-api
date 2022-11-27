<?php

namespace App\Traits;

use App\Constants\TransactionType;
use Carbon\Carbon;
use Illuminate\Http\Response;

trait Generators
{

    /**
     * Generates a 16 character long alphanumeric reference string
     *
     * @return string
     */
    public function generateReference($type): string
    {
        $typeAbbr = "";
        switch ($type) {
            case TransactionType::ADD_CARD:
                $typeAbbr = 'ADDC';
                break;
            case TransactionType::CREDIT_WALLET:
                $typeAbbr = 'CWAL';
                break;
            case TransactionType::PAYOUT:
                $typeAbbr = 'PAYO';
                break;
            case TransactionType::VDEPOSIT:
                $typeAbbr = 'VDEP';
                break;
            case TransactionType::VWITHDRAWAL:
                $typeAbbr = 'VWIT';
                break;
            default:
                $typeAbbr = 'NOTA';
                break;
        }
        $noOfCharacters = 6;
        $setOfCharactersToSelectFrom = '123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ';
        return $typeAbbr . Carbon::now()->format('ymd') . substr(str_shuffle(str_repeat($setOfCharactersToSelectFrom, $noOfCharacters)), 0, $noOfCharacters);
    }

    public function generateTransCode(): string
    {
        return substr(str_shuffle(str_repeat('123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ', 6)), 0, 6);
    }
}
