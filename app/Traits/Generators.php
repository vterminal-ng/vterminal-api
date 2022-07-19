<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait Generators
{

    /**
     * Generates a 16 character long alphanumeric reference string
     *
     * @return string
     */
    public function generateReference(): string
    {
        $noOfCharacters = 16;
        $setOfCharactersToSelectFrom = '123456789ABCDEFGHIJKLMNOPQRSTUVWSYZ';
        return substr(str_shuffle(str_repeat($setOfCharactersToSelectFrom, $noOfCharacters)), 0, $noOfCharacters);
    }
}
