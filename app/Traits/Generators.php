<?php

namespace App\Traits;

use App\Constants\TransactionType;
use App\Constants\BillsPayment;
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

    public function generateMerchantCode(): string
    {
        return substr(str_shuffle(str_repeat('123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ', 7)), 0, 7);
    }

    public function generateVtBasicToken() {
        return base64_encode(config('services.vtpass.email').":".config('services.vtpass.password'));
    }

    public function generateRequestID($type): string
    {
        $typeAbbr = "";
        switch ($type) {
            case BillsPayment::AIRTIME:
                $typeAbbr = 'AIR';
                break;
            case BillsPayment::DATA:
                $typeAbbr = 'DAT';
                break;
            case BillsPayment::ELECTRICITY:
                $typeAbbr = 'ELE';
                break;
            case BillsPayment::CABLETV:
                $typeAbbr = 'CTV';
                break;
            case BillsPayment::EXAM:
                $typeAbbr = 'EXAM';
                break;
            default:
                $typeAbbr = 'NOTA';
                break;
        }
        $noOfCharacters = 6;
        $setOfCharactersToSelectFrom = '123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ';
        return Carbon::now()->format('YmdHm') . $typeAbbr . substr(str_shuffle(str_repeat($setOfCharactersToSelectFrom, $noOfCharacters)), 0, $noOfCharacters);
    }

    public function generateUserApiKey(): string
    {
        $chars = substr(str_shuffle(str_repeat('123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ', 6)), 0, 3);
        $nums = floor(microtime(true) * 1000);
        return $chars.$nums;
    }
}
